<?php

namespace App\Http\Controllers;

use App\Models\ServiceInvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerServiceHistoryController extends Controller
{
    /**
     * Display a listing of customer's service invoices.
     */
    public function index(Request $request)
    {
        $customer = Auth::guard('customer')->user()->customer;

        $query = $customer->serviceInvoices()->with(['vehicle', 'paymentTransactions', 'items']);

        // Filter by vehicle if provided
        if ($request->filled('vehicle_no')) {
            $query->where('vehicle_no', $request->vehicle_no);
        }

        // Filter by service type if provided
        if ($request->filled('service_type')) {
            $query->where('service_type', $request->service_type);
        }

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
                          ->havingRaw('SUM(amount) >= (SELECT grand_total FROM service_invoices WHERE id = service_invoices.id)');
                    });
                    break;
                case 'partial':
                    $query->whereHas('paymentTransactions', function ($q) {
                        $q->where('type', 'cash_in')
                          ->where('status', 'completed')
                          ->havingRaw('SUM(amount) > 0 AND SUM(amount) < (SELECT grand_total FROM service_invoices WHERE id = service_invoices.id)');
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
                  ->orWhere('vehicle_no', 'like', "%$search%")
                  ->orWhere('notes', 'like', "%$search%");
            });
        }

        $serviceInvoices = $query->latest('invoice_date')->paginate(10)->withQueryString();

        // Get customer's vehicles for filter dropdown
        $customerVehicles = $customer->vehicles()->where('status', true)->get();

        // Calculate summary statistics
        $totalServices = $customer->serviceInvoices()->count();
        $totalAmount = $customer->serviceInvoices()->sum('grand_total');
        $totalPaid = $customer->serviceInvoices()
            ->with('paymentTransactions')
            ->get()
            ->sum(function ($invoice) {
                return $invoice->getTotalPayments();
            });
        $lastServiceDate = $customer->serviceInvoices()->latest('invoice_date')->first()?->invoice_date;

        return view('customer.services.index', compact(
            'serviceInvoices',
            'customerVehicles',
            'totalServices',
            'totalAmount',
            'totalPaid',
            'lastServiceDate'
        ));
    }

    /**
     * Display the specified service invoice.
     */
    public function show(ServiceInvoice $serviceInvoice)
    {
        $customer = Auth::guard('customer')->user()->customer;

        // Ensure the service invoice belongs to the authenticated customer
        if ($serviceInvoice->customer_id !== $customer->custom_id) {
            abort(403, 'Unauthorized access to service invoice.');
        }

        $serviceInvoice->load([
            'vehicle',
            'items.jobType',
            'items.product',
            'paymentTransactions' => function ($query) {
                $query->where('status', 'completed')->orderBy('transaction_date', 'desc');
            }
        ]);

        // Calculate payment summary
        $totalPayments = $serviceInvoice->getTotalPayments();
        $outstandingAmount = $serviceInvoice->getOutstandingAmount();
        $paymentStatus = $serviceInvoice->getPaymentStatus();

        // Separate job and spare items
        $jobItems = $serviceInvoice->jobItems;
        $spareItems = $serviceInvoice->spareItems;

        return view('customer.services.show', compact(
            'serviceInvoice',
            'totalPayments',
            'outstandingAmount',
            'paymentStatus',
            'jobItems',
            'spareItems'
        ));
    }

    /**
     * Download service invoice PDF (placeholder for future implementation)
     */
    public function downloadPdf(ServiceInvoice $serviceInvoice)
    {
        $customer = Auth::guard('customer')->user()->customer;

        // Ensure the service invoice belongs to the authenticated customer
        if ($serviceInvoice->customer_id !== $customer->custom_id) {
            abort(403, 'Unauthorized access to service invoice.');
        }

        // TODO: Implement PDF generation
        return redirect()->back()->with('info', 'PDF download feature coming soon!');
    }
}
