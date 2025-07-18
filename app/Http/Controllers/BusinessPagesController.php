<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\BlogPost;
use App\Models\CaseStudy;
use App\Models\Testimonial;
use App\Models\Feature;
use App\Models\PricingPlan;
use App\Models\Partner;
use App\Models\Job;
use App\Models\ContactMessage;
use App\Models\User;

class BusinessPagesController extends Controller
{
    /**
     * About Us page
     */
    public function aboutUs()
    {
        try {
            $stats = [
                'total_users' => User::count(),
                'active_users' => User::where('account_status', 'active')->count(),
                'total_projects' => DB::table('bio_sites')->count() + DB::table('workspaces')->count(),
                'years_in_business' => now()->year - 2020 // Adjust based on actual founding year
            ];

            $teamMembers = [
                [
                    'name' => 'John Smith',
                    'position' => 'CEO & Founder',
                    'bio' => 'Visionary leader with 15+ years in tech industry',
                    'image' => '/images/team/john-smith.jpg',
                    'social' => [
                        'linkedin' => 'https://linkedin.com/in/johnsmith',
                        'twitter' => 'https://twitter.com/johnsmith'
                    ]
                ],
                [
                    'name' => 'Sarah Johnson',
                    'position' => 'CTO',
                    'bio' => 'Technical expert with deep experience in scalable systems',
                    'image' => '/images/team/sarah-johnson.jpg',
                    'social' => [
                        'linkedin' => 'https://linkedin.com/in/sarahjohnson',
                        'github' => 'https://github.com/sarahjohnson'
                    ]
                ],
                [
                    'name' => 'Michael Davis',
                    'position' => 'Head of Design',
                    'bio' => 'Creative director focused on user experience and design',
                    'image' => '/images/team/michael-davis.jpg',
                    'social' => [
                        'dribbble' => 'https://dribbble.com/michaeldavis',
                        'behance' => 'https://behance.net/michaeldavis'
                    ]
                ]
            ];

            $milestones = [
                ['year' => 2020, 'event' => 'Company Founded'],
                ['year' => 2021, 'event' => 'First 1,000 Users'],
                ['year' => 2022, 'event' => 'Series A Funding'],
                ['year' => 2023, 'event' => 'International Expansion'],
                ['year' => 2024, 'event' => 'Enterprise Features Launch'],
                ['year' => 2025, 'event' => 'AI Integration & Advanced Analytics']
            ];

            return view('pages.business.about-us', compact('stats', 'teamMembers', 'milestones'));

        } catch (\Exception $e) {
            Log::error('About Us page failed: ' . $e->getMessage());
            return view('pages.business.about-us', [
                'stats' => ['total_users' => 0, 'active_users' => 0, 'total_projects' => 0, 'years_in_business' => 5],
                'teamMembers' => [],
                'milestones' => []
            ]);
        }
    }

    /**
     * Pricing page
     */
    public function pricing()
    {
        try {
            $plans = PricingPlan::where('is_active', true)->orderBy('base_price')->get();
            
            // If no plans exist, create default ones
            if ($plans->isEmpty()) {
                $plans = $this->createDefaultPricingPlans();
            }

            $features = Feature::where('is_active', true)->orderBy('category')->get();
            $testimonials = Testimonial::where('is_featured', true)->where('is_active', true)->take(3)->get();

            $faq = [
                ['question' => 'Can I change my plan anytime?', 'answer' => 'Yes, you can upgrade or downgrade your plan at any time. Changes take effect immediately.'],
                ['question' => 'Do you offer refunds?', 'answer' => 'We offer a 30-day money-back guarantee for all paid plans.'],
                ['question' => 'Is there a free trial?', 'answer' => 'Yes, all plans come with a 14-day free trial. No credit card required.'],
                ['question' => 'What payment methods do you accept?', 'answer' => 'We accept all major credit cards, PayPal, and bank transfers for enterprise customers.']
            ];

            return view('pages.business.pricing', compact('plans', 'features', 'testimonials', 'faq'));

        } catch (\Exception $e) {
            Log::error('Pricing page failed: ' . $e->getMessage());
            return view('pages.business.pricing', [
                'plans' => [],
                'features' => [],
                'testimonials' => [],
                'faq' => []
            ]);
        }
    }

    /**
     * Features page
     */
    public function features()
    {
        try {
            $features = Feature::where('is_active', true)->orderBy('category')->orderBy('order')->get();
            $caseStudies = CaseStudy::where('is_featured', true)->where('is_active', true)->take(3)->get();

            $categories = $features->groupBy('category');

            return view('pages.business.features', compact('features', 'categories', 'caseStudies'));

        } catch (\Exception $e) {
            Log::error('Features page failed: ' . $e->getMessage());
            return view('pages.business.features', [
                'features' => collect(),
                'categories' => collect(),
                'caseStudies' => collect()
            ]);
        }
    }

    /**
     * Contact Us page
     */
    public function contactUs()
    {
        $contactInfo = [
            'email' => 'hello@mewayz.com',
            'phone' => '+1 (555) 123-4567',
            'address' => '123 Business St, Suite 100, San Francisco, CA 94105',
            'hours' => 'Monday - Friday: 9:00 AM - 6:00 PM PST',
            'social' => [
                'twitter' => 'https://twitter.com/mewayz',
                'linkedin' => 'https://linkedin.com/company/mewayz',
                'facebook' => 'https://facebook.com/mewayz'
            ]
        ];

        return view('pages.business.contact-us', compact('contactInfo'));
    }

    /**
     * Submit contact form
     */
    public function submitContact(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'subject' => 'required|string|max:255',
                'message' => 'required|string|max:2000',
                'company' => 'nullable|string|max:255',
                'phone' => 'nullable|string|max:20'
            ]);

            ContactMessage::create([
                'name' => $request->name,
                'email' => $request->email,
                'subject' => $request->subject,
                'message' => $request->message,
                'company' => $request->company,
                'phone' => $request->phone,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'status' => 'new'
            ]);

            return redirect()->back()->with('success', 'Thank you for your message! We\'ll get back to you soon.');

        } catch (\Exception $e) {
            Log::error('Contact form submission failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Sorry, there was an error sending your message. Please try again.');
        }
    }

    /**
     * Blog page
     */
    public function blog()
    {
        try {
            $posts = BlogPost::where('is_published', true)
                ->orderBy('published_at', 'desc')
                ->paginate(12);

            $categories = BlogPost::where('is_published', true)
                ->select('category')
                ->distinct()
                ->pluck('category');

            $featuredPosts = BlogPost::where('is_published', true)
                ->where('is_featured', true)
                ->orderBy('published_at', 'desc')
                ->take(3)
                ->get();

            return view('pages.business.blog', compact('posts', 'categories', 'featuredPosts'));

        } catch (\Exception $e) {
            Log::error('Blog page failed: ' . $e->getMessage());
            return view('pages.business.blog', [
                'posts' => collect(),
                'categories' => collect(),
                'featuredPosts' => collect()
            ]);
        }
    }

    /**
     * Case Studies page
     */
    public function caseStudies()
    {
        try {
            $caseStudies = CaseStudy::where('is_active', true)
                ->orderBy('created_at', 'desc')
                ->paginate(9);

            $industries = CaseStudy::where('is_active', true)
                ->select('industry')
                ->distinct()
                ->pluck('industry');

            return view('pages.business.case-studies', compact('caseStudies', 'industries'));

        } catch (\Exception $e) {
            Log::error('Case Studies page failed: ' . $e->getMessage());
            return view('pages.business.case-studies', [
                'caseStudies' => collect(),
                'industries' => collect()
            ]);
        }
    }

    /**
     * Testimonials page
     */
    public function testimonials()
    {
        try {
            $testimonials = Testimonial::where('is_active', true)
                ->orderBy('created_at', 'desc')
                ->paginate(12);

            $ratings = Testimonial::where('is_active', true)
                ->selectRaw('AVG(rating) as average, COUNT(*) as total')
                ->first();

            return view('pages.business.testimonials', compact('testimonials', 'ratings'));

        } catch (\Exception $e) {
            Log::error('Testimonials page failed: ' . $e->getMessage());
            return view('pages.business.testimonials', [
                'testimonials' => collect(),
                'ratings' => (object) ['average' => 0, 'total' => 0]
            ]);
        }
    }

    /**
     * Careers page
     */
    public function careers()
    {
        try {
            $jobs = Job::where('is_active', true)
                ->orderBy('created_at', 'desc')
                ->get();

            $departments = Job::where('is_active', true)
                ->select('department')
                ->distinct()
                ->pluck('department');

            $companyValues = [
                'Innovation' => 'We push the boundaries of what\'s possible',
                'Collaboration' => 'We work together to achieve great things',
                'Quality' => 'We deliver excellence in everything we do',
                'Growth' => 'We invest in our people and their development'
            ];

            return view('pages.business.careers', compact('jobs', 'departments', 'companyValues'));

        } catch (\Exception $e) {
            Log::error('Careers page failed: ' . $e->getMessage());
            return view('pages.business.careers', [
                'jobs' => collect(),
                'departments' => collect(),
                'companyValues' => []
            ]);
        }
    }

    /**
     * Partners page
     */
    public function partners()
    {
        try {
            $partners = Partner::where('is_active', true)
                ->orderBy('tier')
                ->orderBy('name')
                ->get();

            $partnerTiers = Partner::where('is_active', true)
                ->select('tier')
                ->distinct()
                ->pluck('tier');

            return view('pages.business.partners', compact('partners', 'partnerTiers'));

        } catch (\Exception $e) {
            Log::error('Partners page failed: ' . $e->getMessage());
            return view('pages.business.partners', [
                'partners' => collect(),
                'partnerTiers' => collect()
            ]);
        }
    }

    /**
     * Security page
     */
    public function security()
    {
        $securityFeatures = [
            'Data Encryption' => 'All data is encrypted at rest and in transit using industry-standard AES-256 encryption',
            'Two-Factor Authentication' => 'Optional 2FA for enhanced account security',
            'Regular Security Audits' => 'Quarterly security assessments by third-party experts',
            'SOC 2 Compliance' => 'Type II SOC 2 certification for data security',
            'GDPR Compliance' => 'Full compliance with EU data protection regulations',
            'Regular Backups' => 'Daily automated backups with point-in-time recovery',
            'DDoS Protection' => 'Advanced DDoS protection and rate limiting',
            'SSL Certificates' => 'Extended validation SSL certificates for all domains'
        ];

        $certifications = [
            'SOC 2 Type II',
            'ISO 27001',
            'GDPR Compliant',
            'CCPA Compliant',
            'PCI DSS Level 1'
        ];

        return view('pages.business.security', compact('securityFeatures', 'certifications'));
    }

    /**
     * Create default pricing plans
     */
    private function createDefaultPricingPlans()
    {
        $plans = [
            [
                'name' => 'Starter',
                'slug' => 'starter',
                'description' => 'Perfect for individuals and small projects',
                'base_price' => 9.99,
                'billing_cycle' => 'monthly',
                'features' => ['Up to 5 bio sites', 'Basic analytics', 'Standard support'],
                'is_popular' => false,
                'is_active' => true
            ],
            [
                'name' => 'Professional',
                'slug' => 'professional',
                'description' => 'Ideal for growing businesses and teams',
                'base_price' => 29.99,
                'billing_cycle' => 'monthly',
                'features' => ['Unlimited bio sites', 'Advanced analytics', 'Priority support', 'Custom domains'],
                'is_popular' => true,
                'is_active' => true
            ],
            [
                'name' => 'Enterprise',
                'slug' => 'enterprise',
                'description' => 'For large organizations with custom needs',
                'base_price' => 99.99,
                'billing_cycle' => 'monthly',
                'features' => ['All Professional features', 'Advanced integrations', 'Dedicated support', 'Custom development'],
                'is_popular' => false,
                'is_active' => true
            ]
        ];

        foreach ($plans as $planData) {
            PricingPlan::create($planData);
        }

        return PricingPlan::where('is_active', true)->orderBy('base_price')->get();
    }
}