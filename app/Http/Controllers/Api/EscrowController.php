<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EscrowTransaction;
use App\Models\EscrowDispute;
use App\Models\EscrowMilestone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class EscrowController extends Controller
{
    /**
     * Get all escrow transactions for the authenticated user
     */
    public function index(Request $request)
    {
        try {
            $user = $request->user();
            
            $transactions = EscrowTransaction::where(function($query) use ($user) {
                $query->where('buyer_id', $user->id)
                      ->orWhere('seller_id', $user->id);
            })
            ->with(['buyer', 'seller', 'milestones', 'disputes'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

            return response()->json([
                'success' => true,
                'data' => $transactions,
                'message' => 'Escrow transactions retrieved successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve escrow transactions: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve escrow transactions'
            ], 500);
        }
    }

    /**
     * Create a new escrow transaction
     */
    public function store(Request $request)
    {
        $request->validate([
            'seller_id' => 'required|exists:users,id',
            'item_type' => 'required|in:website,digital_asset,service,physical_good,business',
            'item_title' => 'required|string|max:255',
            'item_description' => 'required|string',
            'total_amount' => 'required|numeric|min:1',
            'currency' => 'required|string|size:3',
            'escrow_fee_percentage' => 'nullable|numeric|min:0|max:10',
            'inspection_period_hours' => 'nullable|integer|min:24|max:720',
            'milestones' => 'nullable|array',
            'milestones.*.title' => 'required_with:milestones|string|max:255',
            'milestones.*.amount' => 'required_with:milestones|numeric|min:0',
            'milestones.*.description' => 'nullable|string',
            'insurance_required' => 'nullable|boolean',
            'insurance_amount' => 'nullable|numeric|min:0',
        ]);

        try {
            $user = $request->user();

            // Verify seller exists and is different from buyer
            if ($user->id == $request->seller_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot create escrow transaction with yourself'
                ], 400);
            }

            // Calculate escrow fee
            $escrowFeePercentage = $request->escrow_fee_percentage ?? 2.5; // Default 2.5%
            $escrowFee = ($request->total_amount * $escrowFeePercentage) / 100;

            // Create escrow transaction
            $transaction = EscrowTransaction::create([
                'buyer_id' => $user->id,
                'seller_id' => $request->seller_id,
                'item_type' => $request->item_type,
                'item_title' => $request->item_title,
                'item_description' => $request->item_description,
                'total_amount' => $request->total_amount,
                'currency' => $request->currency,
                'escrow_fee' => $escrowFee,
                'escrow_fee_percentage' => $escrowFeePercentage,
                'inspection_period_hours' => $request->inspection_period_hours ?? 72,
                'status' => 'pending_funding',
                'insurance_required' => $request->insurance_required ?? false,
                'insurance_amount' => $request->insurance_amount ?? 0,
                'terms_conditions' => $request->terms_conditions,
                'expires_at' => now()->addDays(7), // Transaction expires in 7 days if not funded
            ]);

            // Create milestones if provided
            if ($request->has('milestones') && is_array($request->milestones)) {
                foreach ($request->milestones as $index => $milestone) {
                    EscrowMilestone::create([
                        'escrow_transaction_id' => $transaction->id,
                        'title' => $milestone['title'],
                        'description' => $milestone['description'] ?? '',
                        'amount' => $milestone['amount'],
                        'order' => $index + 1,
                        'status' => 'pending',
                    ]);
                }
            } else {
                // Create single milestone for full amount
                EscrowMilestone::create([
                    'escrow_transaction_id' => $transaction->id,
                    'title' => 'Full Payment',
                    'description' => 'Complete delivery and payment',
                    'amount' => $request->total_amount,
                    'order' => 1,
                    'status' => 'pending',
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Escrow transaction created successfully',
                'data' => $transaction->load(['milestones'])
            ], 201);
        } catch (\Exception $e) {
            Log::error('Failed to create escrow transaction: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create escrow transaction'
            ], 500);
        }
    }

    /**
     * Get a specific escrow transaction
     */
    public function show(Request $request, $id)
    {
        try {
            $user = $request->user();
            
            $transaction = EscrowTransaction::where('id', $id)
                ->where(function($query) use ($user) {
                    $query->where('buyer_id', $user->id)
                          ->orWhere('seller_id', $user->id);
                })
                ->with(['buyer', 'seller', 'milestones', 'disputes', 'documents'])
                ->firstOrFail();

            return response()->json([
                'success' => true,
                'data' => $transaction,
                'message' => 'Escrow transaction retrieved successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve escrow transaction: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Escrow transaction not found'
            ], 404);
        }
    }

    /**
     * Fund escrow transaction (buyer action)
     */
    public function fundTransaction(Request $request, $id)
    {
        $request->validate([
            'payment_method' => 'required|in:stripe,paypal,bank_transfer,crypto',
            'payment_details' => 'nullable|array',
        ]);

        try {
            $user = $request->user();
            
            $transaction = EscrowTransaction::where('id', $id)
                ->where('buyer_id', $user->id)
                ->where('status', 'pending_funding')
                ->firstOrFail();

            // Process payment (integrate with payment processor)
            $paymentResult = $this->processPayment(
                $transaction->total_amount + $transaction->escrow_fee,
                $transaction->currency,
                $request->payment_method,
                $request->payment_details
            );

            if ($paymentResult['success']) {
                $transaction->update([
                    'status' => 'funded',
                    'payment_method' => $request->payment_method,
                    'payment_id' => $paymentResult['payment_id'],
                    'funded_at' => now(),
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Escrow transaction funded successfully',
                    'data' => $transaction
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment failed: ' . $paymentResult['error']
                ], 400);
            }
        } catch (\Exception $e) {
            Log::error('Failed to fund escrow transaction: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fund escrow transaction'
            ], 500);
        }
    }

    /**
     * Deliver item/service (seller action)
     */
    public function deliverItem(Request $request, $id)
    {
        $request->validate([
            'delivery_notes' => 'nullable|string|max:1000',
            'delivery_proof' => 'nullable|array',
            'milestone_id' => 'nullable|exists:escrow_milestones,id',
        ]);

        try {
            $user = $request->user();
            
            $transaction = EscrowTransaction::where('id', $id)
                ->where('seller_id', $user->id)
                ->where('status', 'funded')
                ->firstOrFail();

            if ($request->milestone_id) {
                $milestone = EscrowMilestone::where('id', $request->milestone_id)
                    ->where('escrow_transaction_id', $transaction->id)
                    ->firstOrFail();
                
                $milestone->update([
                    'status' => 'delivered',
                    'delivered_at' => now(),
                    'delivery_notes' => $request->delivery_notes,
                    'delivery_proof' => $request->delivery_proof,
                ]);

                // Check if all milestones are delivered
                $pendingMilestones = $transaction->milestones()->where('status', '!=', 'delivered')->count();
                if ($pendingMilestones == 0) {
                    $transaction->update([
                        'status' => 'delivered',
                        'delivered_at' => now(),
                        'inspection_deadline' => now()->addHours($transaction->inspection_period_hours),
                    ]);
                }
            } else {
                $transaction->update([
                    'status' => 'delivered',
                    'delivered_at' => now(),
                    'inspection_deadline' => now()->addHours($transaction->inspection_period_hours),
                    'delivery_notes' => $request->delivery_notes,
                    'delivery_proof' => $request->delivery_proof,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Item delivered successfully',
                'data' => $transaction->load(['milestones'])
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to deliver item: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to deliver item'
            ], 500);
        }
    }

    /**
     * Accept delivery (buyer action)
     */
    public function acceptDelivery(Request $request, $id)
    {
        $request->validate([
            'feedback_rating' => 'nullable|integer|min:1|max:5',
            'feedback_comment' => 'nullable|string|max:500',
            'milestone_id' => 'nullable|exists:escrow_milestones,id',
        ]);

        try {
            $user = $request->user();
            
            $transaction = EscrowTransaction::where('id', $id)
                ->where('buyer_id', $user->id)
                ->where('status', 'delivered')
                ->firstOrFail();

            if ($request->milestone_id) {
                $milestone = EscrowMilestone::where('id', $request->milestone_id)
                    ->where('escrow_transaction_id', $transaction->id)
                    ->firstOrFail();
                
                $milestone->update([
                    'status' => 'accepted',
                    'accepted_at' => now(),
                ]);

                // Check if all milestones are accepted
                $pendingMilestones = $transaction->milestones()->where('status', '!=', 'accepted')->count();
                if ($pendingMilestones == 0) {
                    $this->completePayout($transaction);
                }
            } else {
                $this->completePayout($transaction);
            }

            return response()->json([
                'success' => true,
                'message' => 'Delivery accepted successfully',
                'data' => $transaction->load(['milestones'])
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to accept delivery: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to accept delivery'
            ], 500);
        }
    }

    /**
     * Create dispute
     */
    public function createDispute(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|in:not_delivered,not_as_described,damaged,unauthorized_charges,other',
            'description' => 'required|string|max:1000',
            'evidence' => 'nullable|array',
            'requested_resolution' => 'required|in:full_refund,partial_refund,replacement,completion',
        ]);

        try {
            $user = $request->user();
            
            $transaction = EscrowTransaction::where('id', $id)
                ->where(function($query) use ($user) {
                    $query->where('buyer_id', $user->id)
                          ->orWhere('seller_id', $user->id);
                })
                ->whereIn('status', ['funded', 'delivered'])
                ->firstOrFail();

            // Check if dispute already exists
            $existingDispute = EscrowDispute::where('escrow_transaction_id', $transaction->id)
                ->where('status', '!=', 'resolved')
                ->first();

            if ($existingDispute) {
                return response()->json([
                    'success' => false,
                    'message' => 'A dispute is already active for this transaction'
                ], 400);
            }

            $dispute = EscrowDispute::create([
                'escrow_transaction_id' => $transaction->id,
                'initiated_by' => $user->id,
                'reason' => $request->reason,
                'description' => $request->description,
                'evidence' => $request->evidence ?? [],
                'requested_resolution' => $request->requested_resolution,
                'status' => 'open',
            ]);

            $transaction->update(['status' => 'disputed']);

            return response()->json([
                'success' => true,
                'message' => 'Dispute created successfully',
                'data' => $dispute
            ], 201);
        } catch (\Exception $e) {
            Log::error('Failed to create dispute: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create dispute'
            ], 500);
        }
    }

    /**
     * Get escrow statistics
     */
    public function getStatistics(Request $request)
    {
        try {
            $user = $request->user();
            
            $stats = [
                'total_transactions' => EscrowTransaction::where(function($query) use ($user) {
                    $query->where('buyer_id', $user->id)->orWhere('seller_id', $user->id);
                })->count(),
                
                'total_volume' => EscrowTransaction::where(function($query) use ($user) {
                    $query->where('buyer_id', $user->id)->orWhere('seller_id', $user->id);
                })->where('status', 'completed')->sum('total_amount'),
                
                'success_rate' => $this->calculateSuccessRate($user->id),
                
                'pending_transactions' => EscrowTransaction::where(function($query) use ($user) {
                    $query->where('buyer_id', $user->id)->orWhere('seller_id', $user->id);
                })->whereIn('status', ['pending_funding', 'funded', 'delivered'])->count(),
                
                'dispute_rate' => $this->calculateDisputeRate($user->id),
                
                'average_transaction_value' => EscrowTransaction::where(function($query) use ($user) {
                    $query->where('buyer_id', $user->id)->orWhere('seller_id', $user->id);
                })->avg('total_amount'),
            ];

            return response()->json([
                'success' => true,
                'data' => $stats,
                'message' => 'Escrow statistics retrieved successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve escrow statistics: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve escrow statistics'
            ], 500);
        }
    }

    /**
     * Process payment (simplified for demo)
     */
    private function processPayment($amount, $currency, $method, $details)
    {
        // In production, integrate with actual payment processors
        // This is a simplified mock for demo purposes
        return [
            'success' => true,
            'payment_id' => 'pay_' . Str::random(24),
            'amount' => $amount,
            'currency' => $currency,
        ];
    }

    /**
     * Complete payout to seller
     */
    private function completePayout($transaction)
    {
        $transaction->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        // In production, transfer funds to seller
        // Log the payout for record keeping
        Log::info("Escrow payout completed for transaction {$transaction->id}: {$transaction->total_amount} {$transaction->currency}");
    }

    /**
     * Calculate success rate for user
     */
    private function calculateSuccessRate($userId)
    {
        $total = EscrowTransaction::where(function($query) use ($userId) {
            $query->where('buyer_id', $userId)->orWhere('seller_id', $userId);
        })->whereIn('status', ['completed', 'disputed'])->count();

        if ($total == 0) return 100;

        $successful = EscrowTransaction::where(function($query) use ($userId) {
            $query->where('buyer_id', $userId)->orWhere('seller_id', $userId);
        })->where('status', 'completed')->count();

        return round(($successful / $total) * 100, 2);
    }

    /**
     * Calculate dispute rate for user
     */
    private function calculateDisputeRate($userId)
    {
        $total = EscrowTransaction::where(function($query) use ($userId) {
            $query->where('buyer_id', $userId)->orWhere('seller_id', $userId);
        })->count();

        if ($total == 0) return 0;

        $disputed = EscrowTransaction::where(function($query) use ($userId) {
            $query->where('buyer_id', $userId)->orWhere('seller_id', $userId);
        })->where('status', 'disputed')->count();

        return round(($disputed / $total) * 100, 2);
    }
}