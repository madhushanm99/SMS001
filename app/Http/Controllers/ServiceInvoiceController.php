<?php

namespace App\Http\Controllers;

use App\Models\ServiceInvoice;
use App\Models\ServiceInvoiceItem;
use App\Models\Customer;
use App\Models\Vehicle;
use App\Models\JobTypes;
use App\Models\Products;
use App\Models\PaymentTransaction;
use App\Mail\InvoiceMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;

class ServiceInvoiceController extends Controller
{
    public function index(Request $request)
    {
        $query = ServiceInvoice::with(['customer', 'vehicle']);

        // Search functionality
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('invoice_no', 'like', "%$search%")
                  ->orWhere('vehicle_no', 'like', "%$search%")
                  ->orWhereHas('customer', function ($c) use ($search) {
                      $c->where('name', 'like', "%$search%")
                        ->orWhere('phone', 'like', "%$search%");
                  });
            });
        }

        // Status filter
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $invoices = $query->latest()->paginate(10)->withQueryString();

        if ($request->ajax()) {
            return view('service_invoices.table', compact('invoices'))->render();
        }

        return view('service_invoices.index', compact('invoices'));
    }

    public function create()
    {
        // Clear any existing session data
        session()->forget(['service_invoice_job_items', 'service_invoice_spare_items']);
        
        return view('service_invoices.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|string|exists:customers,custom_id',
            'vehicle_no' => 'nullable|string',
            'mileage' => 'nullable|integer|min:0',
            'notes' => 'nullable|string',
        ]);

        $jobItems = session('service_invoice_job_items', []);
        $spareItems = session('service_invoice_spare_items', []);

        if (empty($jobItems) && empty($spareItems)) {
            return back()->with('error', 'Please add at least one job type or spare part.');
        }

        DB::transaction(function () use ($request, $jobItems, $spareItems) {
            $invoice = ServiceInvoice::create([
                'invoice_no' => ServiceInvoice::generateInvoiceNo(),
                'customer_id' => $request->customer_id,
                'vehicle_no' => $request->vehicle_no,
                'mileage' => $request->mileage,
                'invoice_date' => now()->toDateString(),
                'notes' => $request->notes,
                'status' => 'hold',
                'created_by' => Auth::user()->name,
            ]);

            $lineNo = 1;

            // Add job items
            foreach ($jobItems as $item) {
                ServiceInvoiceItem::create([
                    'service_invoice_id' => $invoice->id,
                    'line_no' => $lineNo++,
                    'item_type' => 'job',
                    'item_id' => $item['item_id'],
                    'item_name' => $item['description'],
                    'qty' => $item['qty'],
                    'unit_price' => $item['price'],
                    'discount' => 0,
                    'line_total' => $item['line_total'],
                ]);
            }

            // Add spare items
            foreach ($spareItems as $item) {
                ServiceInvoiceItem::create([
                    'service_invoice_id' => $invoice->id,
                    'line_no' => $lineNo++,
                    'item_type' => 'spare',
                    'item_id' => $item['item_id'],
                    'item_name' => $item['description'],
                    'qty' => $item['qty'],
                    'unit_price' => $item['price'],
                    'discount' => 0,
                    'line_total' => $item['line_total'],
                ]);
            }

            $invoice->calculateTotals();
        });

        // Clear session data
        session()->forget(['service_invoice_job_items', 'service_invoice_spare_items']);

        return redirect()->route('service_invoices.index')->with('success', 'Service invoice created successfully.');
    }

    public function show(ServiceInvoice $serviceInvoice)
    {
        $serviceInvoice->load(['customer', 'vehicle', 'items', 'paymentTransactions']);
        return view('service_invoices.show', compact('serviceInvoice'));
    }

    public function edit(ServiceInvoice $serviceInvoice)
    {
        if ($serviceInvoice->status === 'finalized') {
            return back()->with('error', 'Cannot edit finalized invoices.');
        }

        // Load items into session for editing
        $jobItems = $serviceInvoice->jobItems->map(function ($item) {
            return [
                'item_id' => $item->item_id,
                'description' => $item->item_name,
                'qty' => $item->qty,
                'price' => $item->unit_price,
                'line_total' => $item->line_total,
            ];
        })->toArray();

        $spareItems = $serviceInvoice->spareItems->map(function ($item) {
            return [
                'item_id' => $item->item_id,
                'description' => $item->item_name,
                'qty' => $item->qty,
                'price' => $item->unit_price,
                'line_total' => $item->line_total,
            ];
        })->toArray();

        session(['edit_service_invoice_job_items' => $jobItems]);
        session(['edit_service_invoice_spare_items' => $spareItems]);

        return view('service_invoices.edit', compact('serviceInvoice'));
    }

    public function update(Request $request, ServiceInvoice $serviceInvoice)
    {
        if ($serviceInvoice->status === 'finalized') {
            return back()->with('error', 'Cannot update finalized invoices.');
        }

        $request->validate([
            'customer_id' => 'required|string|exists:customers,custom_id',
            'vehicle_no' => 'nullable|string',
            'mileage' => 'nullable|integer|min:0',
            'notes' => 'nullable|string',
        ]);

        $jobItems = session('edit_service_invoice_job_items', []);
        $spareItems = session('edit_service_invoice_spare_items', []);

        if (empty($jobItems) && empty($spareItems)) {
            return back()->with('error', 'Please add at least one job type or spare part.');
        }

        DB::transaction(function () use ($request, $serviceInvoice, $jobItems, $spareItems) {
            $serviceInvoice->update([
                'customer_id' => $request->customer_id,
                'vehicle_no' => $request->vehicle_no,
                'mileage' => $request->mileage,
                'notes' => $request->notes,
            ]);

            // Delete existing items
            $serviceInvoice->items()->delete();

            $lineNo = 1;

            // Add job items
            foreach ($jobItems as $item) {
                ServiceInvoiceItem::create([
                    'service_invoice_id' => $serviceInvoice->id,
                    'line_no' => $lineNo++,
                    'item_type' => 'job',
                    'item_id' => $item['item_id'],
                    'item_name' => $item['description'],
                    'qty' => $item['qty'],
                    'unit_price' => $item['price'],
                    'discount' => 0,
                    'line_total' => $item['line_total'],
                ]);
            }

            // Add spare items
            foreach ($spareItems as $item) {
                ServiceInvoiceItem::create([
                    'service_invoice_id' => $serviceInvoice->id,
                    'line_no' => $lineNo++,
                    'item_type' => 'spare',
                    'item_id' => $item['item_id'],
                    'item_name' => $item['description'],
                    'qty' => $item['qty'],
                    'unit_price' => $item['price'],
                    'discount' => 0,
                    'line_total' => $item['line_total'],
                ]);
            }

            $serviceInvoice->calculateTotals();
        });

        // Clear session data
        session()->forget(['edit_service_invoice_job_items', 'edit_service_invoice_spare_items']);

        return redirect()->route('service_invoices.index')->with('success', 'Service invoice updated successfully.');
    }

    public function finalize(ServiceInvoice $serviceInvoice)
    {
        if (!$serviceInvoice->canBeFinalized()) {
            return back()->with('error', 'Invoice cannot be finalized. It must be on hold and have at least one item.');
        }

        $serviceInvoice->finalize();

        return back()->with('success', 'Invoice finalized successfully. You can now add payments.');
    }

    public function addPayment(Request $request, ServiceInvoice $serviceInvoice)
    {
        if ($serviceInvoice->status !== 'finalized') {
            return back()->with('error', 'Can only add payments to finalized invoices.');
        }

        return redirect()->route('payment_transactions.create', [
            'type' => 'service_invoice',
            'reference_id' => $serviceInvoice->id
        ]);
    }

    public function destroy(ServiceInvoice $serviceInvoice)
    {
        if ($serviceInvoice->status === 'finalized') {
            return back()->with('error', 'Cannot delete finalized invoices.');
        }

        $serviceInvoice->delete();
        return back()->with('success', 'Service invoice deleted successfully.');
    }

    // PDF Generation
    public function pdf(ServiceInvoice $serviceInvoice)
    {
        $serviceInvoice->load(['customer', 'vehicle', 'items']);
        
        $pdf = Pdf::loadView('service_invoices.pdf', compact('serviceInvoice'));
        return $pdf->download("service_invoice_{$serviceInvoice->invoice_no}.pdf");
    }

    // Email Invoice
    public function email(Request $request, ServiceInvoice $serviceInvoice)
    {
        $request->validate([
            'email' => 'required|email',
            'message' => 'nullable|string',
        ]);

        $serviceInvoice->load(['customer', 'vehicle', 'items']);
        
        $pdf = Pdf::loadView('service_invoices.pdf', compact('serviceInvoice'));
        
        Mail::to($request->email)->send(new InvoiceMail($serviceInvoice, $pdf->output(), $request->message));

        return back()->with('success', 'Invoice emailed successfully.');
    }

    // AJAX methods for item management
    public function customerSearch(Request $request)
    {
        $term = $request->get('term', '');
        
        $customers = Customer::where('status', true)
            ->where(function ($query) use ($term) {
                $query->where('name', 'like', "%$term%")
                      ->orWhere('phone', 'like', "%$term%")
                      ->orWhere('custom_id', 'like', "%$term%");
            })
            ->limit(10)
            ->get()
            ->map(function ($customer) {
                return [
                    'id' => $customer->custom_id,
                    'text' => "{$customer->name} ({$customer->phone})",
                ];
            });

        return response()->json($customers);
    }

    public function vehicleSearch(Request $request)
    {
        $customerId = $request->get('customer_id');
        $term = $request->get('q', '');

        $query = Vehicle::where('status', true);

        if ($customerId) {
            // Find customer by custom_id and get their actual id
            $customer = Customer::where('custom_id', $customerId)->first();
            if ($customer) {
                $query->where('customer_id', $customer->id);
            }
        }

        if ($term) {
            $query->where('vehicle_no', 'like', "%$term%");
        }

        $vehicles = $query->limit(10)
            ->get()
            ->map(function ($vehicle) {
                return [
                    'id' => $vehicle->vehicle_no,
                    'text' => $vehicle->vehicle_no,
                ];
            });

        return response()->json($vehicles);
    }

    public function jobSearch(Request $request)
    {
        $term = $request->get('term', '');
        
        $jobs = JobTypes::where('status', true)
            ->where('jobType', 'like', "%$term%")
            ->limit(10)
            ->get()
            ->map(function ($job) {
                return [
                    'id' => $job->jobCustomID,
                    'text' => $job->jobType,
                    'price' => $job->salesPrice,
                ];
            });

        return response()->json($jobs);
    }

    public function itemSearch(Request $request)
    {
        $term = $request->get('term', '');
        
        $items = Products::where('status', true)
            ->where('item_Name', 'like', "%$term%")
            ->limit(10)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->item_ID,
                    'text' => $item->item_Name,
                    'price' => $item->sales_Price,
                ];
            });

        return response()->json($items);
    }

    // Session management for job items
    public function addJobItem(Request $request)
    {
        $request->validate([
            'item_id' => 'required|string',
            'description' => 'required|string',
            'qty' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0'
        ]);

        $item = [
            'item_id' => $request->item_id,
            'description' => $request->description,
            'qty' => $request->qty,
            'price' => $request->price,
            'line_total' => $request->qty * $request->price
        ];

        $sessionKey = $request->has('edit_mode') ? 'edit_service_invoice_job_items' : 'service_invoice_job_items';
        $items = session()->get($sessionKey, []);
        $items[] = $item;
        session([$sessionKey => $items]);

        return response()->json(['success' => true, 'items' => $items]);
    }

    public function removeJobItem(Request $request)
    {
        $request->validate(['index' => 'required|integer']);

        $sessionKey = $request->has('edit_mode') ? 'edit_service_invoice_job_items' : 'service_invoice_job_items';
        $items = session()->get($sessionKey, []);
        
        if (isset($items[$request->index])) {
            array_splice($items, $request->index, 1);
            session([$sessionKey => $items]);
        }

        return response()->json(['success' => true, 'items' => $items]);
    }

    public function getJobItems(Request $request)
    {
        $sessionKey = $request->has('edit_mode') ? 'edit_service_invoice_job_items' : 'service_invoice_job_items';
        $items = session()->get($sessionKey, []);
        return response()->json(['success' => true, 'items' => $items]);
    }

    // Session management for spare items
    public function addSpareItem(Request $request)
    {
        $request->validate([
            'item_id' => 'required|string',
            'description' => 'required|string',
            'qty' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0'
        ]);

        $item = [
            'item_id' => $request->item_id,
            'description' => $request->description,
            'qty' => $request->qty,
            'price' => $request->price,
            'line_total' => $request->qty * $request->price
        ];

        $sessionKey = $request->has('edit_mode') ? 'edit_service_invoice_spare_items' : 'service_invoice_spare_items';
        $items = session()->get($sessionKey, []);
        $items[] = $item;
        session([$sessionKey => $items]);

        return response()->json(['success' => true, 'items' => $items]);
    }

    public function removeSpareItem(Request $request)
    {
        $request->validate(['index' => 'required|integer']);

        $sessionKey = $request->has('edit_mode') ? 'edit_service_invoice_spare_items' : 'service_invoice_spare_items';
        $items = session()->get($sessionKey, []);
        
        if (isset($items[$request->index])) {
            array_splice($items, $request->index, 1);
            session([$sessionKey => $items]);
        }

        return response()->json(['success' => true, 'items' => $items]);
    }

    public function getSpareItems(Request $request)
    {
        $sessionKey = $request->has('edit_mode') ? 'edit_service_invoice_spare_items' : 'service_invoice_spare_items';
        $items = session()->get($sessionKey, []);
        return response()->json(['success' => true, 'items' => $items]);
    }
} 