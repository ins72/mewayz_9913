<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Affiliate;
use App\Models\AffiliateReferral;
use App\Models\AffiliateCommission;
use App\Models\AffiliatePayment;
use App\Models\AffiliateLink;
use App\Models\User;
use App\Services\AffiliateService;
use App\Services\NotificationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AffiliateController extends Controller
{
    protected $affiliateService;
    protected $notificationService;

    public function __construct(AffiliateService $affiliateService, NotificationService $notificationService)
    {
        $this->affiliateService = $affiliateService;
        $this->notificationService = $notificationService;
    }

    /**
     * Get affiliate dashboard data
     */
    public function getDashboard(Request $request)
    {
        try {
            $user = $request->user();
            $affiliate = Affiliate::where('user_id', $user->id)->first();

            if (!$affiliate) {
                return response()->json([
                    'success' => false,
                    'message' => 'User is not registered as an affiliate'
                ], 404);
            }

            $stats = $this->affiliateService->getAffiliateStats($affiliate);
            $recentActivity = $this->affiliateService->getRecentActivity($affiliate);

            return response()->json([
                'success' => true,
                'data' => [
                    'affiliate' => [
                        'id' => $affiliate->id,
                        'status' => $affiliate->status,
                        'commission_rate' => $affiliate->commission_rate,
                        'tier' => $affiliate->tier,
                        'referral_code' => $affiliate->referral_code,
                        'created_at' => $affiliate->created_at
                    ],
                    'stats' => $stats,
                    'recent_activity' => $recentActivity
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching affiliate dashboard: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch affiliate dashboard'
            ], 500);
        }
    }

    /**
     * Apply to become an affiliate
     */
    public function applyToBecome(Request $request)
    {
        $request->validate([
            'website' => 'nullable|url',
            'social_media' => 'nullable|array',
            'marketing_experience' => 'required|string|max:1000',
            'traffic_sources' => 'required|array',
            'monthly_traffic' => 'required|integer|min:0',
            'audience_description' => 'required|string|max:1000',
            'why_join' => 'required|string|max:1000'
        ]);

        DB::beginTransaction();

        try {
            $user = $request->user();

            // Check if user already has an affiliate account
            $existingAffiliate = Affiliate::where('user_id', $user->id)->first();
            if ($existingAffiliate) {
                return response()->json([
                    'success' => false,
                    'message' => 'User already has an affiliate account'
                ], 400);
            }

            // Create affiliate application
            $affiliate = Affiliate::create([
                'user_id' => $user->id,
                'referral_code' => $this->generateUniqueReferralCode(),
                'status' => 'pending',
                'commission_rate' => 30, // Default 30%
                'tier' => 'bronze',
                'application_data' => [
                    'website' => $request->website,
                    'social_media' => $request->social_media,
                    'marketing_experience' => $request->marketing_experience,
                    'traffic_sources' => $request->traffic_sources,
                    'monthly_traffic' => $request->monthly_traffic,
                    'audience_description' => $request->audience_description,
                    'why_join' => $request->why_join
                ],
                'applied_at' => now()
            ]);

            DB::commit();

            // Notify admin about new application
            $this->notificationService->sendAffiliateApplicationToAdmin($affiliate);

            return response()->json([
                'success' => true,
                'data' => [
                    'affiliate_id' => $affiliate->id,
                    'status' => $affiliate->status,
                    'referral_code' => $affiliate->referral_code
                ],
                'message' => 'Affiliate application submitted successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error submitting affiliate application: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit affiliate application'
            ], 500);
        }
    }

    /**
     * Get affiliate links
     */
    public function getLinks(Request $request)
    {
        try {
            $user = $request->user();
            $affiliate = Affiliate::where('user_id', $user->id)->firstOrFail();

            $links = AffiliateLink::where('affiliate_id', $affiliate->id)
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($link) {
                    return [
                        'id' => $link->id,
                        'name' => $link->name,
                        'url' => $link->url,
                        'clicks' => $link->clicks,
                        'conversions' => $link->conversions,
                        'conversion_rate' => $link->clicks > 0 ? ($link->conversions / $link->clicks) * 100 : 0,
                        'created_at' => $link->created_at
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $links
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching affiliate links: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch affiliate links'
            ], 500);
        }
    }

    /**
     * Create affiliate link
     */
    public function createLink(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'target_url' => 'required|url',
            'campaign' => 'nullable|string|max:100',
            'medium' => 'nullable|string|max:100',
            'source' => 'nullable|string|max:100'
        ]);

        try {
            $user = $request->user();
            $affiliate = Affiliate::where('user_id', $user->id)->firstOrFail();

            $link = AffiliateLink::create([
                'affiliate_id' => $affiliate->id,
                'name' => $request->name,
                'target_url' => $request->target_url,
                'url' => $this->generateAffiliateUrl($affiliate->referral_code, $request->target_url, [
                    'campaign' => $request->campaign,
                    'medium' => $request->medium,
                    'source' => $request->source
                ]),
                'campaign' => $request->campaign,
                'medium' => $request->medium,
                'source' => $request->source,
                'clicks' => 0,
                'conversions' => 0
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $link->id,
                    'name' => $link->name,
                    'url' => $link->url,
                    'created_at' => $link->created_at
                ],
                'message' => 'Affiliate link created successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error creating affiliate link: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create affiliate link'
            ], 500);
        }
    }

    /**
     * Get referrals
     */
    public function getReferrals(Request $request)
    {
        try {
            $user = $request->user();
            $affiliate = Affiliate::where('user_id', $user->id)->firstOrFail();

            $referrals = AffiliateReferral::where('affiliate_id', $affiliate->id)
                ->with('user')
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            return response()->json([
                'success' => true,
                'data' => $referrals
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching referrals: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch referrals'
            ], 500);
        }
    }

    /**
     * Get commissions
     */
    public function getCommissions(Request $request)
    {
        try {
            $user = $request->user();
            $affiliate = Affiliate::where('user_id', $user->id)->firstOrFail();

            $commissions = AffiliateCommission::where('affiliate_id', $affiliate->id)
                ->with(['referral.user', 'subscription'])
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            return response()->json([
                'success' => true,
                'data' => $commissions
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching commissions: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch commissions'
            ], 500);
        }
    }

    /**
     * Get payments
     */
    public function getPayments(Request $request)
    {
        try {
            $user = $request->user();
            $affiliate = Affiliate::where('user_id', $user->id)->firstOrFail();

            $payments = AffiliatePayment::where('affiliate_id', $affiliate->id)
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            return response()->json([
                'success' => true,
                'data' => $payments
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching payments: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch payments'
            ], 500);
        }
    }

    /**
     * Request payout
     */
    public function requestPayout(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:50', // Minimum $50 payout
            'payment_method' => 'required|in:paypal,bank_transfer,stripe',
            'payment_details' => 'required|array'
        ]);

        DB::beginTransaction();

        try {
            $user = $request->user();
            $affiliate = Affiliate::where('user_id', $user->id)->firstOrFail();

            // Check available balance
            $availableBalance = $affiliate->getAvailableBalance();
            if ($request->amount > $availableBalance) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient balance for payout'
                ], 400);
            }

            // Create payout request
            $payment = AffiliatePayment::create([
                'affiliate_id' => $affiliate->id,
                'amount' => $request->amount,
                'payment_method' => $request->payment_method,
                'payment_details' => $request->payment_details,
                'status' => 'pending',
                'requested_at' => now()
            ]);

            // Update affiliate balance
            $affiliate->increment('total_paid', $request->amount);
            $affiliate->increment('pending_balance', -$request->amount);

            DB::commit();

            // Notify admin about payout request
            $this->notificationService->sendPayoutRequestToAdmin($payment);

            return response()->json([
                'success' => true,
                'data' => [
                    'payment_id' => $payment->id,
                    'amount' => $payment->amount,
                    'status' => $payment->status
                ],
                'message' => 'Payout request submitted successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error requesting payout: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to request payout'
            ], 500);
        }
    }

    /**
     * Get marketing materials
     */
    public function getMarketingMaterials(Request $request)
    {
        try {
            $materials = [
                'banners' => [
                    [
                        'id' => 1,
                        'name' => 'Header Banner 728x90',
                        'size' => '728x90',
                        'url' => asset('marketing/banner-728x90.jpg'),
                        'html' => '<img src="' . asset('marketing/banner-728x90.jpg') . '" alt="Mewayz" />'
                    ],
                    [
                        'id' => 2,
                        'name' => 'Square Banner 300x300',
                        'size' => '300x300',
                        'url' => asset('marketing/banner-300x300.jpg'),
                        'html' => '<img src="' . asset('marketing/banner-300x300.jpg') . '" alt="Mewayz" />'
                    ]
                ],
                'logos' => [
                    [
                        'id' => 1,
                        'name' => 'Mewayz Logo - Light',
                        'url' => asset('marketing/logo-light.png'),
                        'format' => 'PNG'
                    ],
                    [
                        'id' => 2,
                        'name' => 'Mewayz Logo - Dark',
                        'url' => asset('marketing/logo-dark.png'),
                        'format' => 'PNG'
                    ]
                ],
                'email_templates' => [
                    [
                        'id' => 1,
                        'name' => 'Welcome Email Template',
                        'subject' => 'Transform Your Business with Mewayz',
                        'content' => 'Pre-written email template for affiliate marketing...'
                    ]
                ],
                'social_media' => [
                    [
                        'id' => 1,
                        'platform' => 'Facebook',
                        'content' => 'Transform your business with Mewayz - the all-in-one creator platform! 🚀'
                    ],
                    [
                        'id' => 2,
                        'platform' => 'Twitter',
                        'content' => 'Just discovered @Mewayz - amazing platform for creators! 🔥'
                    ]
                ]
            ];

            return response()->json([
                'success' => true,
                'data' => $materials
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching marketing materials: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch marketing materials'
            ], 500);
        }
    }

    /**
     * Track affiliate click
     */
    public function trackClick(Request $request, $referralCode)
    {
        try {
            $affiliate = Affiliate::where('referral_code', $referralCode)->first();
            
            if (!$affiliate) {
                return redirect(config('app.url'));
            }

            // Track the click
            $this->affiliateService->trackClick($affiliate, $request);

            // Redirect to the target URL
            $targetUrl = $request->get('url', config('app.url'));
            return redirect($targetUrl);

        } catch (\Exception $e) {
            Log::error('Error tracking affiliate click: ' . $e->getMessage());
            return redirect(config('app.url'));
        }
    }

    /**
     * Generate unique referral code
     */
    private function generateUniqueReferralCode()
    {
        do {
            $code = strtoupper(Str::random(8));
        } while (Affiliate::where('referral_code', $code)->exists());

        return $code;
    }

    /**
     * Generate affiliate URL
     */
    private function generateAffiliateUrl($referralCode, $targetUrl, $params = [])
    {
        $url = route('affiliate.track', ['referralCode' => $referralCode]);
        $url .= '?url=' . urlencode($targetUrl);
        
        foreach ($params as $key => $value) {
            if ($value) {
                $url .= '&' . $key . '=' . urlencode($value);
            }
        }

        return $url;
    }
}