<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\PaymentMethod;
use App\Models\FinancialTransaction;
use App\Models\TaxCalculation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AdvancedFinancialController extends Controller
{
    /**
     * Get comprehensive financial dashboard
     */
    public function getFinancialDashboard(Request $request)
    {
        try {
            $user = $request->user();
            
            $dashboard = [
                'revenue_metrics' => $this->getRevenueMetrics($user->id),
                'expense_metrics' => $this->getExpenseMetrics($user->id),
                'profit_metrics' => $this->getProfitMetrics($user->id),
                'invoice_metrics' => $this->getInvoiceMetrics($user->id),
                'cash_flow' => $this->getCashFlow($user->id),
                'payment_methods' => $this->getPaymentMethodStats($user->id),
                'tax_summary' => $this->getTaxSummary($user->id),
                'financial_goals' => $this->getFinancialGoals($user->id),
                'forecasts' => $this->getFinancialForecasts($user->id),
            ];

            return response()->json([
                'success' => true,
                'data' => $dashboard,
                'message' => 'Financial dashboard retrieved successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve financial dashboard: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve financial dashboard'
            ], 500);
        }
    }

    /**
     * Create invoice
     */
    public function createInvoice(Request $request)
    {
        $request->validate([
            'client_name' => 'required|string|max:255',
            'client_email' => 'required|email',
            'client_address' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string|max:255',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.tax_rate' => 'nullable|numeric|min:0|max:100',
            'currency' => 'required|string|size:3',
            'due_date' => 'required|date|after:today',
            'notes' => 'nullable|string|max:1000',
            'payment_terms' => 'nullable|string|max:500',
            'tax_calculation' => 'nullable|array',
        ]);

        try {
            $user = $request->user();
            
            // Calculate totals
            $subtotal = 0;
            $totalTax = 0;
            
            foreach ($request->items as $item) {
                $lineTotal = $item['quantity'] * $item['unit_price'];
                $subtotal += $lineTotal;
                
                if (isset($item['tax_rate']) && $item['tax_rate'] > 0) {
                    $totalTax += ($lineTotal * $item['tax_rate']) / 100;
                }
            }
            
            $total = $subtotal + $totalTax;

            $invoice = Invoice::create([
                'user_id' => $user->id,
                'invoice_number' => $this->generateInvoiceNumber(),
                'client_name' => $request->client_name,
                'client_email' => $request->client_email,
                'client_address' => $request->client_address,
                'items' => $request->items,
                'subtotal' => $subtotal,
                'tax_amount' => $totalTax,
                'total_amount' => $total,
                'currency' => $request->currency,
                'status' => 'draft',
                'due_date' => Carbon::parse($request->due_date),
                'notes' => $request->notes,
                'payment_terms' => $request->payment_terms,
                'tax_calculation' => $request->tax_calculation ?? [],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Invoice created successfully',
                'data' => $invoice
            ], 201);
        } catch (\Exception $e) {
            Log::error('Failed to create invoice: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create invoice'
            ], 500);
        }
    }

    /**
     * Get invoices with filtering
     */
    public function getInvoices(Request $request)
    {
        $request->validate([
            'status' => 'nullable|in:draft,sent,paid,overdue,cancelled',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'search' => 'nullable|string|max:255',
        ]);

        try {
            $user = $request->user();
            
            $query = Invoice::where('user_id', $user->id);

            if ($request->status) {
                $query->where('status', $request->status);
            }

            if ($request->start_date) {
                $query->where('created_at', '>=', Carbon::parse($request->start_date));
            }

            if ($request->end_date) {
                $query->where('created_at', '<=', Carbon::parse($request->end_date)->endOfDay());
            }

            if ($request->search) {
                $query->where(function($q) use ($request) {
                    $q->where('client_name', 'like', '%' . $request->search . '%')
                      ->orWhere('invoice_number', 'like', '%' . $request->search . '%')
                      ->orWhere('client_email', 'like', '%' . $request->search . '%');
                });
            }

            $invoices = $query->orderBy('created_at', 'desc')->paginate(20);

            return response()->json([
                'success' => true,
                'data' => $invoices,
                'message' => 'Invoices retrieved successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve invoices: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve invoices'
            ], 500);
        }
    }

    /**
     * Send invoice to client
     */
    public function sendInvoice(Request $request, $invoiceId)
    {
        try {
            $user = $request->user();
            
            $invoice = Invoice::where('id', $invoiceId)
                ->where('user_id', $user->id)
                ->firstOrFail();

            if ($invoice->status !== 'draft') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only draft invoices can be sent'
                ], 400);
            }

            // Send invoice email (implementation needed)
            $this->sendInvoiceEmail($invoice);

            $invoice->update([
                'status' => 'sent',
                'sent_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Invoice sent successfully',
                'data' => $invoice
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send invoice: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to send invoice'
            ], 500);
        }
    }

    /**
     * Record payment for invoice
     */
    public function recordPayment(Request $request, $invoiceId)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|string|max:100',
            'payment_date' => 'required|date',
            'reference_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            $user = $request->user();
            
            $invoice = Invoice::where('id', $invoiceId)
                ->where('user_id', $user->id)
                ->firstOrFail();

            if ($invoice->status === 'paid') {
                return response()->json([
                    'success' => false,
                    'message' => 'Invoice is already paid'
                ], 400);
            }

            $remainingAmount = $invoice->total_amount - $invoice->paid_amount;
            
            if ($request->amount > $remainingAmount) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment amount exceeds remaining balance'
                ], 400);
            }

            // Create financial transaction
            FinancialTransaction::create([
                'user_id' => $user->id,
                'invoice_id' => $invoice->id,
                'type' => 'payment_received',
                'amount' => $request->amount,
                'currency' => $invoice->currency,
                'payment_method' => $request->payment_method,
                'reference_number' => $request->reference_number,
                'description' => 'Payment for invoice ' . $invoice->invoice_number,
                'transaction_date' => Carbon::parse($request->payment_date),
                'notes' => $request->notes,
                'status' => 'completed',
            ]);

            // Update invoice
            $newPaidAmount = $invoice->paid_amount + $request->amount;
            $newStatus = $newPaidAmount >= $invoice->total_amount ? 'paid' : 'partially_paid';

            $invoice->update([
                'paid_amount' => $newPaidAmount,
                'status' => $newStatus,
                'paid_at' => $newStatus === 'paid' ? now() : $invoice->paid_at,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Payment recorded successfully',
                'data' => $invoice->fresh()
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to record payment: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to record payment'
            ], 500);
        }
    }

    /**
     * Get tax calculations
     */
    public function calculateTax(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'customer_location' => 'required|array',
            'customer_location.country' => 'required|string|size:2',
            'customer_location.state' => 'nullable|string|max:10',
            'customer_location.zip' => 'nullable|string|max:20',
            'product_type' => 'nullable|string|max:100',
            'business_location' => 'required|array',
            'business_location.country' => 'required|string|size:2',
            'business_location.state' => 'nullable|string|max:10',
        ]);

        try {
            $taxCalculation = $this->performTaxCalculation(
                $request->amount,
                $request->customer_location,
                $request->business_location,
                $request->product_type
            );

            return response()->json([
                'success' => true,
                'data' => $taxCalculation,
                'message' => 'Tax calculated successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to calculate tax: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to calculate tax'
            ], 500);
        }
    }

    /**
     * Get financial reports
     */
    public function getFinancialReports(Request $request)
    {
        $request->validate([
            'report_type' => 'required|in:profit_loss,cash_flow,balance_sheet,tax_summary',
            'period' => 'required|in:this_month,last_month,this_quarter,last_quarter,this_year,custom',
            'start_date' => 'required_if:period,custom|date',
            'end_date' => 'required_if:period,custom|date|after_or_equal:start_date',
        ]);

        try {
            $user = $request->user();
            $dateRange = $this->getDateRange($request->period, $request->start_date, $request->end_date);
            
            $report = match($request->report_type) {
                'profit_loss' => $this->generateProfitLossReport($user->id, $dateRange),
                'cash_flow' => $this->generateCashFlowReport($user->id, $dateRange),
                'balance_sheet' => $this->generateBalanceSheetReport($user->id, $dateRange),
                'tax_summary' => $this->generateTaxSummaryReport($user->id, $dateRange),
            };

            return response()->json([
                'success' => true,
                'data' => [
                    'report_type' => $request->report_type,
                    'period' => $request->period,
                    'date_range' => $dateRange,
                    'report_data' => $report,
                    'generated_at' => now()->toISOString(),
                ],
                'message' => 'Financial report generated successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to generate financial report: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate financial report'
            ], 500);
        }
    }

    /**
     * Get payment method analytics
     */
    public function getPaymentMethodAnalytics(Request $request)
    {
        try {
            $user = $request->user();
            
            $analytics = [
                'payment_method_breakdown' => $this->getPaymentMethodBreakdown($user->id),
                'payment_trends' => $this->getPaymentTrends($user->id),
                'processing_fees' => $this->getProcessingFees($user->id),
                'failed_payments' => $this->getFailedPayments($user->id),
                'refunds_chargebacks' => $this->getRefundsChargebacks($user->id),
            ];

            return response()->json([
                'success' => true,
                'data' => $analytics,
                'message' => 'Payment method analytics retrieved successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve payment analytics: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve payment analytics'
            ], 500);
        }
    }

    // Helper methods

    private function generateInvoiceNumber(): string
    {
        $year = date('Y');
        $month = date('m');
        $sequence = Invoice::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->count() + 1;
        
        return "INV-{$year}{$month}-" . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    private function sendInvoiceEmail($invoice)
    {
        // Implementation for sending invoice email
        Log::info("Invoice {$invoice->invoice_number} sent to {$invoice->client_email}");
    }

    private function performTaxCalculation($amount, $customerLocation, $businessLocation, $productType)
    {
        // Simplified tax calculation - in production, integrate with tax services like TaxJar
        $taxRate = 0;
        $taxRules = [];

        // US tax calculation example
        if ($customerLocation['country'] === 'US' && $businessLocation['country'] === 'US') {
            $stateTaxRates = [
                'CA' => 7.25,
                'NY' => 8.0,
                'TX' => 6.25,
                'FL' => 6.0,
                'WA' => 6.5,
            ];

            $state = $customerLocation['state'] ?? '';
            $taxRate = $stateTaxRates[$state] ?? 0;
            
            if ($taxRate > 0) {
                $taxRules[] = [
                    'type' => 'state_sales_tax',
                    'rate' => $taxRate,
                    'jurisdiction' => $state,
                ];
            }
        }

        $taxAmount = ($amount * $taxRate) / 100;

        return [
            'subtotal' => $amount,
            'tax_rate' => $taxRate,
            'tax_amount' => round($taxAmount, 2),
            'total' => round($amount + $taxAmount, 2),
            'tax_rules' => $taxRules,
            'currency' => 'USD',
        ];
    }

    private function getDateRange($period, $startDate = null, $endDate = null)
    {
        return match($period) {
            'this_month' => [
                'start' => now()->startOfMonth()->toDateString(),
                'end' => now()->endOfMonth()->toDateString(),
            ],
            'last_month' => [
                'start' => now()->subMonth()->startOfMonth()->toDateString(),
                'end' => now()->subMonth()->endOfMonth()->toDateString(),
            ],
            'this_quarter' => [
                'start' => now()->startOfQuarter()->toDateString(),
                'end' => now()->endOfQuarter()->toDateString(),
            ],
            'last_quarter' => [
                'start' => now()->subQuarter()->startOfQuarter()->toDateString(),
                'end' => now()->subQuarter()->endOfQuarter()->toDateString(),
            ],
            'this_year' => [
                'start' => now()->startOfYear()->toDateString(),
                'end' => now()->endOfYear()->toDateString(),
            ],
            'custom' => [
                'start' => $startDate,
                'end' => $endDate,
            ],
        };
    }

    // Report generation methods (simplified for demo)
    
    private function getRevenueMetrics($userId)
    {
        return [
            'total_revenue' => 125640.50,
            'monthly_growth' => 12.5,
            'average_invoice_value' => 1240.30,
            'recurring_revenue' => 45780.20,
        ];
    }

    private function getExpenseMetrics($userId)
    {
        return [
            'total_expenses' => 45320.80,
            'fixed_expenses' => 25000.00,
            'variable_expenses' => 20320.80,
            'expense_categories' => [
                'Software' => 12000.00,
                'Marketing' => 8500.00,
                'Operations' => 4820.80,
            ],
        ];
    }

    private function getProfitMetrics($userId)
    {
        return [
            'gross_profit' => 80319.70,
            'net_profit' => 58240.30,
            'profit_margin' => 46.3,
            'ebitda' => 62150.45,
        ];
    }

    private function getInvoiceMetrics($userId)
    {
        return [
            'total_invoices' => 145,
            'paid_invoices' => 128,
            'overdue_invoices' => 12,
            'average_payment_time' => 18.5,
        ];
    }

    private function getCashFlow($userId)
    {
        return [
            'current_balance' => 45780.30,
            'projected_income' => 28450.60,
            'projected_expenses' => 15200.40,
            'net_cash_flow' => 13250.20,
        ];
    }

    private function getPaymentMethodStats($userId)
    {
        return [
            'credit_card' => 65.5,
            'bank_transfer' => 25.3,
            'paypal' => 7.2,
            'other' => 2.0,
        ];
    }

    private function getTaxSummary($userId)
    {
        return [
            'total_tax_collected' => 8450.75,
            'tax_owed' => 2100.30,
            'tax_paid' => 6350.45,
            'next_filing_date' => '2025-04-15',
        ];
    }

    private function getFinancialGoals($userId)
    {
        return [
            'revenue_goal' => 500000,
            'revenue_progress' => 68.5,
            'profit_goal' => 150000,
            'profit_progress' => 72.3,
        ];
    }

    private function getFinancialForecasts($userId)
    {
        return [
            'next_month_revenue' => 42500.00,
            'next_quarter_revenue' => 135000.00,
            'yearly_projection' => 485000.00,
        ];
    }

    private function generateProfitLossReport($userId, $dateRange)
    {
        return [
            'revenue' => 125640.50,
            'cost_of_goods_sold' => 45319.80,
            'gross_profit' => 80320.70,
            'operating_expenses' => 32150.40,
            'net_income' => 48170.30,
        ];
    }

    private function generateCashFlowReport($userId, $dateRange)
    {
        return [
            'operating_activities' => 45230.20,
            'investing_activities' => -12450.00,
            'financing_activities' => -5780.30,
            'net_cash_flow' => 26999.90,
        ];
    }

    private function generateBalanceSheetReport($userId, $dateRange)
    {
        return [
            'assets' => [
                'current_assets' => 125640.50,
                'fixed_assets' => 45780.30,
                'total_assets' => 171420.80,
            ],
            'liabilities' => [
                'current_liabilities' => 32150.40,
                'long_term_liabilities' => 15780.20,
                'total_liabilities' => 47930.60,
            ],
            'equity' => 123490.20,
        ];
    }

    private function generateTaxSummaryReport($userId, $dateRange)
    {
        return [
            'sales_tax_collected' => 8450.75,
            'income_tax_owed' => 12150.30,
            'deductible_expenses' => 25780.40,
            'net_tax_liability' => 15820.65,
        ];
    }

    private function getPaymentMethodBreakdown($userId)
    {
        return [
            'stripe' => ['amount' => 75420.30, 'percentage' => 65.5],
            'paypal' => ['amount' => 28950.40, 'percentage' => 25.2],
            'bank_transfer' => ['amount' => 10680.90, 'percentage' => 9.3],
        ];
    }

    private function getPaymentTrends($userId)
    {
        return [
            'monthly_volume' => 42380.50,
            'growth_rate' => 15.7,
            'average_transaction' => 245.80,
        ];
    }

    private function getProcessingFees($userId)
    {
        return [
            'total_fees' => 2180.45,
            'fee_percentage' => 2.9,
            'fee_breakdown' => [
                'stripe' => 1450.30,
                'paypal' => 730.15,
            ],
        ];
    }

    private function getFailedPayments($userId)
    {
        return [
            'failed_count' => 12,
            'failed_amount' => 3450.80,
            'failure_rate' => 2.8,
        ];
    }

    private function getRefundsChargebacks($userId)
    {
        return [
            'refunds' => ['count' => 5, 'amount' => 1250.40],
            'chargebacks' => ['count' => 1, 'amount' => 299.99],
        ];
    }
}