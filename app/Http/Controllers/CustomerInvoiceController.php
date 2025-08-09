<?php

namespace App\Http\Controllers;

use App\Models\SalesInvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerInvoiceController extends Controller
{
    /**
     * Display a listing of customer's invoices.
     */
    public function index(Request $request)
    {
        $customer = Auth::guard('customer')->user()->customer;

        $query = $customer->salesInvoices()->with(['items', 'paymentTransactions']);

        // Filter by status if provided
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by payment status if provided
        if ($request->filled('payment_status')) {
            switch ($request->payment_status) {
                case 'paid':
                    $query->whereHas('paymentTransactions', function ($q) {
                        $q->where('type', 'cash_in')
                          ->where('status', 'completed')
                          ->havingRaw('SUM(amount) >= (SELECT grand_total FROM sales_invoices WHERE id = sales_invoices.id)');
                    });
                    break;
                case 'partial':
                    $query->whereHas('paymentTransactions', function ($q) {
                        $q->where('type', 'cash_in')
                          ->where('status', 'completed')
                          ->havingRaw('SUM(amount) > 0 AND SUM(amount) < (SELECT grand_total FROM sales_invoices WHERE id = sales_invoices.id)');
                    });
                    break;
                case 'unpaid':
                    $query->whereDoesntHave('paymentTransactions', function ($q) {
                        $q->where('type', 'cash_in')->where('status', 'completed');
                    });
                    break;
            }
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_no', 'like', "%$search%")
                  ->orWhere('notes', 'like', "%$search%");
            });
        }

        $invoices = $query->latest('invoice_date')->paginate(10)->withQueryString();

        // Calculate summary statistics
        $totalInvoices = $customer->salesInvoices()->count();
        $totalAmount = $customer->salesInvoices()->sum('grand_total');
        $totalPaid = $customer->salesInvoices()
            ->with('paymentTransactions')
            ->get()
            ->sum(function ($invoice) {
                return $invoice->getTotalPayments();
            });
        $creditBalance = $customer->balance_credit;

        return view('customer.invoices.index', compact(
            'invoices',
            'totalInvoices',
            'totalAmount',
            'totalPaid',
            'creditBalance'
        ));
    }

    /**
     * Display the specified invoice.
     */
    public function show(SalesInvoice $invoice)
    {
        $customer = Auth::guard('customer')->user()->customer;

        // Ensure the invoice belongs to the authenticated customer
        if ($invoice->customer_id !== $customer->custom_id) {
            abort(403, 'Unauthorized access to invoice.');
        }

        $invoice->load(['items.product', 'paymentTransactions' => function ($query) {
            $query->where('status', 'completed')->orderBy('transaction_date', 'desc');
        }, 'returns.items']);

        // Calculate payment summary
        $totalPayments = $invoice->getTotalPayments();
        $outstandingAmount = $invoice->getOutstandingAmount();
        $paymentStatus = $invoice->getPaymentStatus();
        $paymentStatusColor = $invoice->getPaymentStatusColor();

        // Calculate return summary
        $totalReturns = $invoice->getTotalReturns();
        $totalRefunds = $invoice->getTotalRefunds();

        return view('customer.invoices.show', compact(
            'invoice',
            'totalPayments',
            'outstandingAmount',
            'paymentStatus',
            'paymentStatusColor',
            'totalReturns',
            'totalRefunds'
        ));
    }

    /**
     * Download invoice PDF (placeholder for future implementation)
     */
    public function downloadPdf(SalesInvoice $invoice)
    {
        $customer = Auth::guard('customer')->user()->customer;

        // Ensure the invoice belongs to the authenticated customer
        if ($invoice->customer_id !== $customer->custom_id) {
            abort(403, 'Unauthorized access to invoice.');
        }

        // TODO: Implement PDF generation
        return redirect()->back()->with('info', 'PDF download feature coming soon!');
    }
}
