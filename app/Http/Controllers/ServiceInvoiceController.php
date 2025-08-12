<?php

namespace App\Http\Controllers;

use App\Models\ServiceInvoice;
use App\Models\ServiceInvoiceItem;
use App\Jobs\CalculateNextServiceSchedule;
use App\Models\Customer;
use App\Models\Vehicle;
use App\Models\JobTypes;
use App\Models\Products;
use App\Models\Stock;
use App\Models\PaymentTransaction;
use App\Models\PaymentMethod;
use App\Models\PaymentCategory;
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

        // If a vehicle is selected, ensure it's approved
        if ($request->filled('vehicle_no')) {
            $vehicle = Vehicle::where('vehicle_no', $request->vehicle_no)->first();
            if (!$vehicle) {
                return back()->with('error', 'Selected vehicle not found.')->withInput();
            }
            if (!$vehicle->is_approved) {
                return back()->with('error', 'Selected vehicle is pending approval and cannot be used for invoices.')->withInput();
            }
        }

        $jobItems = session('service_invoice_job_items', []);
        $spareItems = session('service_invoice_spare_items', []);

        if (empty($jobItems) && empty($spareItems)) {
            return back()->with('error', 'Please add at least one job type or spare part.');
        }

        $isFinalize = $request->has('finalize') && $request->finalize == '1';
        $invoice = null;

        $lowStockAlerts = [];
        DB::transaction(function () use ($request, $jobItems, $spareItems, $isFinalize, &$invoice, &$lowStockAlerts) {
            $invoice = ServiceInvoice::create([
                'invoice_no' => ServiceInvoice::generateInvoiceNo(),
                'customer_id' => $request->customer_id,
                'vehicle_no' => $request->vehicle_no,
                'mileage' => $request->mileage,
                'invoice_date' => now()->toDateString(),
                'notes' => $request->notes,
                'status' => $isFinalize ? 'finalized' : 'hold',
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
            $lowStockAlerts = $lowStockAlerts ?? [];
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

            // Determine and set service type based on job items
            $invoice->determineServiceType();

            // If immediately finalized, update customer's last_visit
            if ($isFinalize) {
                // Reduce stock for spare items and check reorder levels
                foreach ($spareItems as $item) {
                    $qty = (int) ($item['qty'] ?? 0);
                    if ($qty > 0) {
                        Stock::reduce($item['item_id'], $qty);
                        $alert = $this->buildReorderAlert($item['item_id'], $item['description'] ?? null);
                        if ($alert) {
                            $lowStockAlerts[] = $alert;
                        }
                    }
                }
                $customer = $invoice->customer; // relation uses custom_id mapping
                if ($customer) {
                    $customer->updateLastVisit($invoice->invoice_date);
                }
            }
        });

        // Dispatch background calculation if finalized
        if ($isFinalize && $invoice) {
            CalculateNextServiceSchedule::dispatch($invoice->id);
        }

        // Clear session data
        session()->forget(['service_invoice_job_items', 'service_invoice_spare_items']);

        if ($isFinalize) {
            // Redirect to payment and PDF options page
            return redirect()->route('service_invoices.finalize_options', $invoice)
                ->with('success', 'Service invoice finalized successfully!')
                ->with('low_stock_alerts', $lowStockAlerts ?? []);
        }

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

        // Clear any existing session data and set new data
        session()->forget(['edit_service_invoice_job_items', 'edit_service_invoice_spare_items']);
        session(['edit_service_invoice_job_items' => $jobItems]);
        session(['edit_service_invoice_spare_items' => $spareItems]);

        // Log for debugging
        \Log::info('Edit invoice session data', [
            'invoice_id' => $serviceInvoice->id,
            'job_items_count' => count($jobItems),
            'spare_items_count' => count($spareItems),
            'job_items' => $jobItems,
            'spare_items' => $spareItems
        ]);

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
            // In edit mode, customer_id and vehicle_no should remain the same
            // Only update editable fields
            $serviceInvoice->update([
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

            // Determine and set service type based on job items
            $serviceInvoice->determineServiceType();
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

        // Reduce stock for spare items and check reorder levels
        $lowStockAlerts = [];
        $spareItems = $serviceInvoice->spareItems()->get();
        foreach ($spareItems as $item) {
            if ($item->qty > 0) {
                Stock::reduce($item->item_id, $item->qty);
                $alert = $this->buildReorderAlert($item->item_id, $item->item_name);
                if ($alert) {
                    $lowStockAlerts[] = $alert;
                }
            }
        }

        $serviceInvoice->finalize();

        // Determine and set service type based on job items when finalizing
        $serviceInvoice->determineServiceType();

        // Update customer's last visit on finalize
        $customer = $serviceInvoice->customer;
        if ($customer) {
            $customer->updateLastVisit($serviceInvoice->invoice_date);
        }

        // Dispatch background calculation
        CalculateNextServiceSchedule::dispatch($serviceInvoice->id);

        $redirect = back()->with('success', 'Invoice finalized successfully. You can now add payments.');
        if (!empty($lowStockAlerts)) {
            $redirect->with('low_stock_alerts', $lowStockAlerts);
        }
        return $redirect;
    }

    public function finalizeOptions(ServiceInvoice $serviceInvoice)
    {
        if ($serviceInvoice->status !== 'finalized') {
            return redirect()->route('service_invoices.index')
                ->with('error', 'Only finalized invoices can access payment and PDF options.');
        }

        $serviceInvoice->load(['customer', 'vehicle', 'items']);
        return view('service_invoices.finalize_options', compact('serviceInvoice'));
    }

    public function addPayment(ServiceInvoice $serviceInvoice)
    {
        if ($serviceInvoice->status !== 'finalized') {
            return redirect()->route('service_invoices.index')
                ->with('error', 'Can only add payments to finalized invoices.');
        }

        $serviceInvoice->load(['customer', 'paymentTransactions']);
        return view('service_invoices.add_payment', compact('serviceInvoice'));
    }

    /**
     * Store a payment for the service invoice.
     */
    public function storePayment(Request $request, ServiceInvoice $serviceInvoice)
    {
        if ($serviceInvoice->status !== 'finalized') {
            return redirect()->route('service_invoices.index')
                ->with('error', 'Can only add payments to finalized invoices.');
        }

        $request->validate([
            'amount' => 'required|numeric|min:0.01|max:' . $serviceInvoice->getOutstandingAmount(),
            'payment_method_id' => 'required|string',
            'payment_date' => 'required|date',
            'reference_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            DB::transaction(function () use ($request, $serviceInvoice) {
                // Get or create the payment method
                $paymentMethodName = ucfirst($request->payment_method_id);
                $paymentMethod = PaymentMethod::firstOrCreate(
                    ['code' => $request->payment_method_id],
                    [
                        'name' => str_replace('_', ' ', ucfirst($request->payment_method_id)),
                        'code' => $request->payment_method_id,
                        'description' => 'Auto-created payment method',
                        'is_active' => true,
                        'requires_reference' => in_array($request->payment_method_id, ['cheque', 'bank_transfer']),
                    ]
                );

                // Get or create a payment category for service invoice payments
                $paymentCategory = PaymentCategory::firstOrCreate(
                    ['code' => 'service_income'],
                    [
                        'name' => 'Service Income',
                        'code' => 'service_income',
                        'type' => 'income',
                        'description' => 'Income from service invoices',
                        'is_active' => true,
                        'sort_order' => 1,
                    ]
                );

                PaymentTransaction::create([
                    'type' => 'cash_in',
                    'amount' => $request->amount,
                    'transaction_date' => $request->payment_date,
                    'transaction_time' => now(),
                    'description' => 'Payment for service invoice #' . $serviceInvoice->invoice_no,
                    'reference_no' => $request->reference_number,
                    'payment_method_id' => $paymentMethod->id, // Use the ID instead of the string value
                    'payment_category_id' => $paymentCategory->id, // Add payment category ID
                    'customer_id' => $serviceInvoice->customer_id,
                    'service_invoice_id' => $serviceInvoice->id,
                    'status' => 'completed',
                    'created_by' => Auth::user()->name ?? Auth::user()->email,
                    'notes' => $request->notes,
                ]);

                // Do not update the invoice status, as it only supports 'hold' and 'finalized'
                // The payment status can be determined using the getPaymentStatus() method
                // We keep the status as 'finalized'
            });

            return redirect()->route('service_invoices.show', $serviceInvoice)
                ->with('success', 'Payment recorded successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error recording payment: ' . $e->getMessage())
                ->withInput();
        }
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

        $pdf = Pdf::loadView('service_invoices.pdf', ['serviceInvoice' => $serviceInvoice,]);
        return $pdf->stream("service_invoice_{$serviceInvoice->invoice_no}.pdf");
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

        Mail::to($request->email)->queue(new InvoiceMail($serviceInvoice, $pdf->output(), $request->message));

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

        // Log the request for debugging
        \Log::info('Vehicle search request', [
            'customer_id' => $customerId,
            'search_term' => $term
        ]);

        $query = Vehicle::where('status', true)->where('is_approved', true);

        if ($customerId) {
            // Find customer by custom_id and get their actual id
            $customer = Customer::where('custom_id', $customerId)->first();
            if ($customer) {
                $query->where('customer_id', $customer->id);
                \Log::info('Filtering vehicles for customer', [
                    'customer_custom_id' => $customerId,
                    'customer_db_id' => $customer->id
                ]);
            } else {
                \Log::warning('Customer not found', ['custom_id' => $customerId]);
                // Return empty array if customer not found
                return response()->json([]);
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
                    'customer_name' => $vehicle->customer->name ?? 'Unknown'
                ];
            });

        \Log::info('Vehicle search results', [
            'count' => $vehicles->count(),
            'vehicles' => $vehicles->toArray()
        ]);

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
                // Include current stock quantity for client-side validation
                $stock = Stock::where('item_ID', $item->item_ID)->first();
                $stockQty = $stock ? (int) $stock->quantity : 0;
                return [
                    'id' => $item->item_ID,
                    'text' => $item->item_Name,
                    'price' => $item->sales_Price,
                    'stock_qty' => $stockQty,
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

        \Log::info('Get job items request', [
            'session_key' => $sessionKey,
            'edit_mode' => $request->has('edit_mode'),
            'items_count' => count($items),
            'items' => $items,
            'all_session_keys' => array_keys(session()->all())
        ]);

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

        // Server-side stock validation: prevent adding items when available stock is 0
        $stock = Stock::where('item_ID', $request->item_id)->first();
        $availableQty = $stock ? (int) $stock->quantity : 0;
        if ($availableQty <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'This item has no stock available',
            ], 200);
        }

        // Also ensure cumulative quantity in the session does not exceed stock
        $sessionKey = $request->has('edit_mode') ? 'edit_service_invoice_spare_items' : 'service_invoice_spare_items';
        $items = session()->get($sessionKey, []);

        $existingQtyForItem = 0;
        foreach ($items as $existing) {
            if (($existing['item_id'] ?? null) === $request->item_id) {
                $existingQtyForItem += (int) ($existing['qty'] ?? 0);
            }
        }

        if ($existingQtyForItem + (int) $request->qty > $availableQty) {
            return response()->json([
                'success' => false,
                'message' => "Insufficient stock. Available: {$availableQty} (already added: {$existingQtyForItem})",
            ], 200);
        }

        $item = [
            'item_id' => $request->item_id,
            'description' => $request->description,
            'qty' => (int) $request->qty,
            'price' => (float) $request->price,
            'line_total' => (float) $request->qty * (float) $request->price
        ];

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

        \Log::info('Get spare items request', [
            'session_key' => $sessionKey,
            'edit_mode' => $request->has('edit_mode'),
            'items_count' => count($items),
            'items' => $items,
            'all_session_keys' => array_keys(session()->all())
        ]);

        return response()->json(['success' => true, 'items' => $items]);
    }

    /**
     * Build a low stock alert message when stock falls to or below reorder level.
     */
    protected function buildReorderAlert(string $itemId, ?string $itemName = null): ?string
    {
        try {
            $product = Products::where('item_ID', $itemId)->first();
            $stock = Stock::where('item_ID', $itemId)->first();
            if (!$product || !$stock) {
                return null;
            }
            $reorderLevel = (int) ($product->reorder_level ?? 0);
            $currentQty = (int) $stock->quantity;
            if ($reorderLevel > 0 && $currentQty <= $reorderLevel) {
                $name = $itemName ?: ($product->item_Name ?? $itemId);
                \App\Services\NotificationService::lowStockReached($itemId, $name, $currentQty, $reorderLevel);
                return "Low stock: {$name} ({$itemId}) qty {$currentQty} ≤ reorder level {$reorderLevel}";
            }
        } catch (\Throwable $e) {
            // Ignore alert errors
        }
        return null;
    }
}
