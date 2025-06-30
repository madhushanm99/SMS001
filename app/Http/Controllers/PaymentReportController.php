<?php

namespace App\Http\Controllers;

use App\Models\PaymentTransaction;
use App\Models\PaymentMethod;
use App\Models\BankAccount;
use App\Models\PaymentCategory;
use App\Models\Customer;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PaymentReportController extends Controller
{
    /**
     * Payment reports dashboard
     */
    public function index(): View
    {
        $summary = $this->getDashboardSummary();
        
        return view('payment_reports.index', compact('summary'));
    }

    /**
     * Cash flow report
     */
    public function cashFlow(Request $request): View
    {
        $dateFrom = $request->get('date_from', now()->subMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', now()->format('Y-m-d'));
        $groupBy = $request->get('group_by', 'daily'); // daily, weekly, monthly

        $cashFlowData = $this->getCashFlowData($dateFrom, $dateTo, $groupBy);
        
        $summary = [
            'total_cash_in' => $cashFlowData['total_cash_in'],
            'total_cash_out' => $cashFlowData['total_cash_out'],
            'net_flow' => $cashFlowData['net_flow'],
            'transaction_count' => $cashFlowData['transaction_count'],
        ];

        return view('payment_reports.cash_flow', compact(
            'cashFlowData',
            'summary',
            'dateFrom',
            'dateTo',
            'groupBy'
        ));
    }

    /**
     * Category wise report
     */
    public function categoryReport(Request $request): View
    {
        $dateFrom = $request->get('date_from', now()->subMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', now()->format('Y-m-d'));
        $type = $request->get('type', 'expense'); // income, expense, both

        $categoryData = $this->getCategoryData($dateFrom, $dateTo, $type);

        return view('payment_reports.category', compact(
            'categoryData',
            'dateFrom',
            'dateTo',
            'type'
        ));
    }

    /**
     * Payment method report
     */
    public function paymentMethodReport(Request $request): View
    {
        $dateFrom = $request->get('date_from', now()->subMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', now()->format('Y-m-d'));

        $methodData = $this->getPaymentMethodData($dateFrom, $dateTo);

        return view('payment_reports.payment_method', compact(
            'methodData',
            'dateFrom',
            'dateTo'
        ));
    }

    /**
     * Bank account report
     */
    public function bankAccountReport(Request $request): View
    {
        $dateFrom = $request->get('date_from', now()->subMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', now()->format('Y-m-d'));

        $bankData = $this->getBankAccountData($dateFrom, $dateTo);

        return view('payment_reports.bank_account', compact(
            'bankData',
            'dateFrom',
            'dateTo'
        ));
    }

    /**
     * Customer payment report
     */
    public function customerReport(Request $request): View
    {
        $dateFrom = $request->get('date_from', now()->subMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', now()->format('Y-m-d'));

        $customerData = $this->getCustomerPaymentData($dateFrom, $dateTo);

        return view('payment_reports.customer', compact(
            'customerData',
            'dateFrom',
            'dateTo'
        ));
    }

    /**
     * Supplier payment report
     */
    public function supplierReport(Request $request): View
    {
        $dateFrom = $request->get('date_from', now()->subMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', now()->format('Y-m-d'));

        $supplierData = $this->getSupplierPaymentData($dateFrom, $dateTo);

        return view('payment_reports.supplier', compact(
            'supplierData',
            'dateFrom',
            'dateTo'
        ));
    }

    /**
     * Outstanding payments report
     */
    public function outstandingReport(): View
    {
        $outstandingInvoices = $this->getOutstandingInvoices();
        $outstandingPOs = $this->getOutstandingPOs();

        $summary = [
            'invoice_count' => $outstandingInvoices['count'],
            'invoice_amount' => $outstandingInvoices['amount'],
            'po_count' => $outstandingPOs['count'],
            'po_amount' => $outstandingPOs['amount'],
            'total_outstanding' => $outstandingInvoices['amount'] + $outstandingPOs['amount'],
        ];

        return view('payment_reports.outstanding', compact(
            'outstandingInvoices',
            'outstandingPOs',
            'summary'
        ));
    }

    /**
     * Monthly comparison report
     */
    public function monthlyComparison(Request $request): View
    {
        $months = $request->get('months', 12);
        
        $comparisonData = $this->getMonthlyComparisonData($months);

        return view('payment_reports.monthly_comparison', compact(
            'comparisonData',
            'months'
        ));
    }

    /**
     * Export cash flow data
     */
    public function exportCashFlow(Request $request): JsonResponse
    {
        $dateFrom = $request->get('date_from', now()->subMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', now()->format('Y-m-d'));
        $format = $request->get('format', 'excel'); // excel, csv, pdf

        try {
            $data = $this->getCashFlowExportData($dateFrom, $dateTo);
            
            // Here you would implement the actual export logic
            // For now, returning the data structure
            
            return response()->json([
                'success' => true,
                'download_url' => '/exports/cash-flow-' . time() . '.' . $format,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Export failed'], 500);
        }
    }

    /**
     * Get analytics data for API
     */
    public function analytics(Request $request): JsonResponse
    {
        $period = $request->get('period', 30); // days
        $type = $request->get('type', 'overview'); // overview, category, payment_method, bank

        try {
            switch ($type) {
                case 'category':
                    $data = $this->getCategoryAnalytics($period);
                    break;
                case 'payment_method':
                    $data = $this->getPaymentMethodAnalytics($period);
                    break;
                case 'bank':
                    $data = $this->getBankAnalytics($period);
                    break;
                default:
                    $data = $this->getOverviewAnalytics($period);
            }

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to get analytics'], 500);
        }
    }

    // Private helper methods

    private function getDashboardSummary(): array
    {
        $today = now();
        $startOfMonth = $today->copy()->startOfMonth();
        $startOfYear = $today->copy()->startOfYear();

        return [
            'today' => [
                'cash_in' => PaymentTransaction::cashIn()->completed()->whereDate('transaction_date', $today)->sum('amount'),
                'cash_out' => PaymentTransaction::cashOut()->completed()->whereDate('transaction_date', $today)->sum('amount'),
                'transactions' => PaymentTransaction::completed()->whereDate('transaction_date', $today)->count(),
            ],
            'month' => [
                'cash_in' => PaymentTransaction::cashIn()->completed()->where('transaction_date', '>=', $startOfMonth)->sum('amount'),
                'cash_out' => PaymentTransaction::cashOut()->completed()->where('transaction_date', '>=', $startOfMonth)->sum('amount'),
                'transactions' => PaymentTransaction::completed()->where('transaction_date', '>=', $startOfMonth)->count(),
            ],
            'year' => [
                'cash_in' => PaymentTransaction::cashIn()->completed()->where('transaction_date', '>=', $startOfYear)->sum('amount'),
                'cash_out' => PaymentTransaction::cashOut()->completed()->where('transaction_date', '>=', $startOfYear)->sum('amount'),
                'transactions' => PaymentTransaction::completed()->where('transaction_date', '>=', $startOfYear)->count(),
            ],
            'bank_balances' => BankAccount::active()->sum('current_balance'),
            'pending_approvals' => PaymentTransaction::pending()->count(),
        ];
    }

    private function getCashFlowData(string $dateFrom, string $dateTo, string $groupBy): array
    {
        $query = PaymentTransaction::completed()
            ->whereBetween('transaction_date', [$dateFrom, $dateTo]);

        $selectField = match($groupBy) {
            'weekly' => "WEEK(transaction_date) as period, YEAR(transaction_date) as year",
            'monthly' => "MONTH(transaction_date) as period, YEAR(transaction_date) as year",
            default => "DATE(transaction_date) as period"
        };

        $cashFlow = $query->select(
                DB::raw($selectField),
                DB::raw('SUM(CASE WHEN type = "cash_in" THEN amount ELSE 0 END) as cash_in'),
                DB::raw('SUM(CASE WHEN type = "cash_out" THEN amount ELSE 0 END) as cash_out'),
                DB::raw('COUNT(*) as transaction_count')
            )
            ->groupBy(DB::raw($groupBy === 'daily' ? 'DATE(transaction_date)' : 'period, year'))
            ->orderBy('period')
            ->get();

        return [
            'data' => $cashFlow,
            'total_cash_in' => $cashFlow->sum('cash_in'),
            'total_cash_out' => $cashFlow->sum('cash_out'),
            'net_flow' => $cashFlow->sum('cash_in') - $cashFlow->sum('cash_out'),
            'transaction_count' => $cashFlow->sum('transaction_count'),
        ];
    }

    private function getCategoryData(string $dateFrom, string $dateTo, string $type): array
    {
        $query = PaymentTransaction::with('paymentCategory')
            ->completed()
            ->whereBetween('transaction_date', [$dateFrom, $dateTo]);

        if ($type !== 'both') {
            $query->where('type', $type === 'income' ? 'cash_in' : 'cash_out');
        }

        $transactions = $query->get();

        $categoryData = $transactions->groupBy('payment_category_id')->map(function ($group) {
            $category = $group->first()->paymentCategory;
            return [
                'category' => $category,
                'amount' => $group->sum('amount'),
                'count' => $group->count(),
                'avg_amount' => $group->avg('amount'),
                'type' => $group->first()->type,
            ];
        })->sortByDesc('amount');

        return [
            'categories' => $categoryData->values(),
            'total_amount' => $categoryData->sum('amount'),
            'total_transactions' => $categoryData->sum('count'),
        ];
    }

    private function getPaymentMethodData(string $dateFrom, string $dateTo): array
    {
        $methodData = PaymentTransaction::with('paymentMethod')
            ->completed()
            ->whereBetween('transaction_date', [$dateFrom, $dateTo])
            ->select(
                'payment_method_id',
                DB::raw('SUM(amount) as total_amount'),
                DB::raw('COUNT(*) as transaction_count'),
                DB::raw('AVG(amount) as avg_amount')
            )
            ->groupBy('payment_method_id')
            ->get();

        return [
            'methods' => $methodData,
            'total_amount' => $methodData->sum('total_amount'),
            'total_transactions' => $methodData->sum('transaction_count'),
        ];
    }

    private function getBankAccountData(string $dateFrom, string $dateTo): array
    {
        $bankData = PaymentTransaction::with('bankAccount')
            ->completed()
            ->whereNotNull('bank_account_id')
            ->whereBetween('transaction_date', [$dateFrom, $dateTo])
            ->select(
                'bank_account_id',
                DB::raw('SUM(CASE WHEN type = "cash_in" THEN amount ELSE 0 END) as cash_in'),
                DB::raw('SUM(CASE WHEN type = "cash_out" THEN amount ELSE 0 END) as cash_out'),
                DB::raw('COUNT(*) as transaction_count')
            )
            ->groupBy('bank_account_id')
            ->get();

        return [
            'accounts' => $bankData,
            'total_cash_in' => $bankData->sum('cash_in'),
            'total_cash_out' => $bankData->sum('cash_out'),
            'total_transactions' => $bankData->sum('transaction_count'),
        ];
    }

    private function getCustomerPaymentData(string $dateFrom, string $dateTo): array
    {
        $customerData = PaymentTransaction::with('customer')
            ->cashIn()
            ->completed()
            ->whereNotNull('customer_id')
            ->whereBetween('transaction_date', [$dateFrom, $dateTo])
            ->select(
                'customer_id',
                DB::raw('SUM(amount) as total_received'),
                DB::raw('COUNT(*) as payment_count'),
                DB::raw('AVG(amount) as avg_payment')
            )
            ->groupBy('customer_id')
            ->orderByDesc('total_received')
            ->get();

        return [
            'customers' => $customerData,
            'total_received' => $customerData->sum('total_received'),
            'total_payments' => $customerData->sum('payment_count'),
        ];
    }

    private function getSupplierPaymentData(string $dateFrom, string $dateTo): array
    {
        $supplierData = PaymentTransaction::with('supplier')
            ->cashOut()
            ->completed()
            ->whereNotNull('supplier_id')
            ->whereBetween('transaction_date', [$dateFrom, $dateTo])
            ->select(
                'supplier_id',
                DB::raw('SUM(amount) as total_paid'),
                DB::raw('COUNT(*) as payment_count'),
                DB::raw('AVG(amount) as avg_payment')
            )
            ->groupBy('supplier_id')
            ->orderByDesc('total_paid')
            ->get();

        return [
            'suppliers' => $supplierData,
            'total_paid' => $supplierData->sum('total_paid'),
            'total_payments' => $supplierData->sum('payment_count'),
        ];
    }

    private function getOutstandingInvoices(): array
    {
        // This would need to be implemented based on your SalesInvoice model
        // For now, returning sample structure
        return [
            'invoices' => collect([]),
            'count' => 0,
            'amount' => 0,
        ];
    }

    private function getOutstandingPOs(): array
    {
        // This would need to be implemented based on your Po model
        // For now, returning sample structure
        return [
            'pos' => collect([]),
            'count' => 0,
            'amount' => 0,
        ];
    }

    private function getMonthlyComparisonData(int $months): array
    {
        $data = [];
        
        for ($i = 0; $i < $months; $i++) {
            $date = now()->subMonths($i);
            $startOfMonth = $date->copy()->startOfMonth();
            $endOfMonth = $date->copy()->endOfMonth();
            
            $cashIn = PaymentTransaction::cashIn()
                ->completed()
                ->whereBetween('transaction_date', [$startOfMonth, $endOfMonth])
                ->sum('amount');
                
            $cashOut = PaymentTransaction::cashOut()
                ->completed()
                ->whereBetween('transaction_date', [$startOfMonth, $endOfMonth])
                ->sum('amount');
                
            $data[] = [
                'month' => $date->format('Y-m'),
                'month_name' => $date->format('M Y'),
                'cash_in' => $cashIn,
                'cash_out' => $cashOut,
                'net_flow' => $cashIn - $cashOut,
            ];
        }
        
        return array_reverse($data);
    }

    private function getCashFlowExportData(string $dateFrom, string $dateTo): array
    {
        return PaymentTransaction::with(['paymentMethod', 'paymentCategory', 'customer', 'supplier'])
            ->completed()
            ->whereBetween('transaction_date', [$dateFrom, $dateTo])
            ->orderBy('transaction_date')
            ->get()
            ->map(function ($transaction) {
                return [
                    'date' => $transaction->transaction_date,
                    'transaction_no' => $transaction->transaction_no,
                    'type' => $transaction->type,
                    'amount' => $transaction->amount,
                    'description' => $transaction->description,
                    'payment_method' => $transaction->paymentMethod?->name,
                    'category' => $transaction->paymentCategory?->name,
                    'customer' => $transaction->customer?->name,
                    'supplier' => $transaction->supplier?->Supp_Name,
                ];
            })->toArray();
    }

    private function getOverviewAnalytics(int $period): array
    {
        $startDate = now()->subDays($period);
        
        return [
            'total_cash_in' => PaymentTransaction::cashIn()->completed()->where('transaction_date', '>=', $startDate)->sum('amount'),
            'total_cash_out' => PaymentTransaction::cashOut()->completed()->where('transaction_date', '>=', $startDate)->sum('amount'),
            'transaction_count' => PaymentTransaction::completed()->where('transaction_date', '>=', $startDate)->count(),
            'avg_transaction' => PaymentTransaction::completed()->where('transaction_date', '>=', $startDate)->avg('amount'),
        ];
    }

    private function getCategoryAnalytics(int $period): array
    {
        // Implementation for category analytics
        return [];
    }

    private function getPaymentMethodAnalytics(int $period): array
    {
        // Implementation for payment method analytics
        return [];
    }

    private function getBankAnalytics(int $period): array
    {
        // Implementation for bank analytics
        return [];
    }
}
