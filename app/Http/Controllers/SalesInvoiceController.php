<?php

namespace App\Http\Controllers;

use App\Models\SalesInvoice;
use App\Models\SalesInvoiceItem;
use App\Models\Customer;
use App\Models\Products;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Mail\InvoiceMail;

class SalesInvoiceController extends Controller
{
    protected function sessionKey(): string
    {
        return 'temp_sales_invoice_items_' . auth()->id();
    }

    public function index(Request $request)
    {
        $query = SalesInvoice::query();

        // Add search filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('invoice_no', 'like', "%{$search}%")
                  ->orWhereHas('customer', function($subQ) use ($search) {
                      $subQ->where('name', 'like', "%{$search}%")
                           ->orWhere('phone', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->whereBetween('invoice_date', [$request->from_date, $request->to_date]);
        }

        // Payment status filter
        if ($request->filled('payment_status')) {
            $paymentStatus = $request->payment_status;

            if ($paymentStatus === 'paid') {
                $query->whereRaw('(SELECT COALESCE(SUM(amount), 0) FROM payment_transactions WHERE sales_invoice_id = sales_invoices.id AND status = "completed" AND type = "cash_in") >= grand_total');
            } elseif ($paymentStatus === 'partially_paid') {
                $query->whereRaw('(SELECT COALESCE(SUM(amount), 0) FROM payment_transactions WHERE sales_invoice_id = sales_invoices.id AND status = "completed" AND type = "cash_in") > 0')
                      ->whereRaw('(SELECT COALESCE(SUM(amount), 0) FROM payment_transactions WHERE sales_invoice_id = sales_invoices.id AND status = "completed" AND type = "cash_in") < grand_total');
            } elseif ($paymentStatus === 'unpaid') {
                $query->whereRaw('(SELECT COALESCE(SUM(amount), 0) FROM payment_transactions WHERE sales_invoice_id = sales_invoices.id AND status = "completed" AND type = "cash_in") = 0');
            }
        }

        $invoices = $query->with(['customer', 'paymentTransactions' => function($query) {
                                $query->where('status', 'completed')
                                      ->where('type', 'cash_in')
                                      ->with('paymentMethod');
                            }])
                         ->orderByDesc('created_at')
                         ->paginate(10);

        if ($request->ajax()) {
            return view('sales_invoices.table', compact('invoices'))->render();
        }

        // Get payment methods and bank accounts for payment prompt
        $paymentMethods = \App\Models\PaymentMethod::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'code', 'is_active']);

        $bankAccounts = \App\Models\BankAccount::orderBy('account_name')
            ->get(['id', 'account_name', 'bank_name']);

        $paymentCategories = \App\Models\PaymentCategory::where('is_active', true)
            ->where('type', 'income')
            ->orderBy('name')
            ->get(['id', 'name', 'description', 'type']);

        return view('sales_invoices.index', compact('invoices', 'paymentMethods', 'bankAccounts', 'paymentCategories'));
    }

    public function create()
    {
        session()->forget($this->sessionKey());

        // Get payment methods and bank accounts for payment prompt
        $paymentMethods = \App\Models\PaymentMethod::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'code', 'is_active']);

        $bankAccounts = \App\Models\BankAccount::orderBy('account_name')
            ->get(['id', 'account_name', 'bank_name']);

        $paymentCategories = \App\Models\PaymentCategory::where('is_active', true)
            ->where('type', 'income')
            ->orderBy('name')
            ->get(['id', 'name', 'description', 'type']);

        return view('sales_invoices.create', compact('paymentMethods', 'bankAccounts', 'paymentCategories'));
    }

    public function searchCustomers(Request $request)
    {
        $term = $request->get('term', '');
        $customers = Customer::where('name', 'like', "%{$term}%")
                           ->orWhere('phone', 'like', "%{$term}%")
                           ->orWhere('custom_id', 'like', "%{$term}%")
                           ->where('status', true)
                           ->limit(10)
                           ->get();

        return response()->json($customers->map(function ($customer) {
            return [
                'id' => $customer->custom_id,
                'text' => $customer->name . ' - ' . $customer->phone,
                'name' => $customer->name,
                'phone' => $customer->phone,
                'address' => $customer->address
            ];
        }));
    }

    public function searchItems(Request $request)
    {
        $term = $request->get('term', '');
        $items = Products::where('item_Name', 'like', "%{$term}%")
                        ->orWhere('item_ID', 'like', "%{$term}%")
                        ->where('status', 1)
                        ->limit(10)
                        ->get();

        return response()->json($items->map(function ($item) {
            // Get current stock
            $stock = Stock::where('item_ID', $item->item_ID)->first();
            $stockQty = $stock ? $stock->quantity : 0;

            return [
                'id' => $item->item_ID,
                'text' => $item->item_Name . ' - ' . $item->item_ID,
                'name' => $item->item_Name,
                'price' => $item->sales_Price,
                'stock_qty' => $stockQty
            ];
        }));
    }

    public function addTempItem(Request $request)
    {
        $request->validate([
            'item_id' => 'required|string',
            'qty' => 'required|integer|min:1',
            'discount' => 'nullable|numeric|min:0|max:100',
        ]);

        $item = Products::where('item_ID', $request->item_id)->first();
        if (!$item) {
            return response()->json(['success' => false, 'message' => 'Item not found']);
        }

        // Check stock availability
        $stock = Stock::where('item_ID', $item->item_ID)->first();
        $availableQty = $stock ? $stock->quantity : 0;

        if ($request->qty > $availableQty) {
            return response()->json(['success' => false, 'message' => 'Insufficient stock. Available: ' . $availableQty]);
        }

        $key = $this->sessionKey();
        $items = session()->get($key, []);

        $discount = $request->discount ?? 0;
        $unitPrice = $item->sales_Price;
        $lineTotal = ($unitPrice * $request->qty) - (($unitPrice * $request->qty * $discount) / 100);

        // Check if item already exists in session
        $existingIndex = null;
        foreach ($items as $index => $existing) {
            if ($existing['item_id'] === $item->item_ID) {
                $existingIndex = $index;
                break;
            }
        }

        if ($existingIndex !== null) {
            // Update existing item
            $items[$existingIndex]['qty'] += $request->qty;
            $items[$existingIndex]['line_total'] = ($unitPrice * $items[$existingIndex]['qty']) -
                                                  (($unitPrice * $items[$existingIndex]['qty'] * $items[$existingIndex]['discount']) / 100);
        } else {
            // Add new item
            $items[] = [
                'item_id' => $item->item_ID,
                'item_name' => $item->item_Name,
                'unit_price' => $unitPrice,
                'qty' => $request->qty,
                'discount' => $discount,
                'line_total' => $lineTotal,
                'stock_qty' => $availableQty
            ];
        }

        session([$key => $items]);

        return response()->json([
            'success' => true,
            'items' => $items,
            'total' => collect($items)->sum('line_total')
        ]);
    }

    public function removeTempItem(Request $request)
    {
        $key = $this->sessionKey();
        $items = session()->get($key, []);

        // Find the item being removed
        $removedItem = null;
        foreach ($items as $item) {
            if ($item['item_id'] === $request->item_id) {
                $removedItem = $item;
                break;
            }
        }

        // If we're editing an invoice and it's finalized, restore stock for removed item
        if ($removedItem && $request->has('invoice_id')) {
            try {
                $invoice = SalesInvoice::find($request->invoice_id);
                if ($invoice && $invoice->status === 'finalized') {
                    // Check if this item was originally in the invoice
                    $originalItem = $invoice->items()->where('item_id', $removedItem['item_id'])->first();
                    if ($originalItem) {
                        // Restore stock for the original quantity
                        $stock = Stock::where('item_ID', $removedItem['item_id'])->first();
                        if ($stock) {
                            $stock->increment('quantity', $originalItem->qty);
                        }
                    }
                }
            } catch (\Exception $e) {
                // Log error but don't fail the removal
                \Log::error('Error restoring stock during item removal: ' . $e->getMessage());
            }
        }

        // Remove item from session
        $items = array_filter($items, function($item) use ($request) {
            return $item['item_id'] !== $request->item_id;
        });

        session([$key => array_values($items)]);

        return response()->json([
            'success' => true,
            'items' => array_values($items),
            'total' => collect($items)->sum('line_total')
        ]);
    }

    public function hold(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $items = session()->get($this->sessionKey(), []);
        if (empty($items)) {
            return response()->json(['success' => false, 'message' => 'No items added']);
        }

        DB::beginTransaction();
        try {
            $invoice = SalesInvoice::create([
                'invoice_no' => SalesInvoice::generateInvoiceNo(),
                'customer_id' => $request->customer_id,
                'invoice_date' => now()->toDateString(),
                'grand_total' => collect($items)->sum('line_total'),
                'notes' => $request->notes,
                'status' => 'hold',
                'created_by' => auth()->user()->name ?? 'System',
            ]);

            foreach ($items as $index => $item) {
                SalesInvoiceItem::create([
                    'sales_invoice_id' => $invoice->id,
                    'line_no' => $index + 1,
                    'item_id' => $item['item_id'],
                    'item_name' => $item['item_name'],
                    'qty' => $item['qty'],
                    'unit_price' => $item['unit_price'],
                    'discount' => $item['discount'],
                    'line_total' => $item['line_total'],
                ]);
            }

            DB::commit();
            session()->forget($this->sessionKey());

            return response()->json([
                'success' => true,
                'message' => 'Invoice saved as hold',
                'invoice_id' => $invoice->id,
                'redirect_url' => route('sales_invoices.index')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error saving invoice: ' . $e->getMessage()]);
        }
    }

    public function finalize(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $items = session()->get($this->sessionKey(), []);
        if (empty($items)) {
            return response()->json(['success' => false, 'message' => 'No items added']);
        }

        DB::beginTransaction();
        try {
            $invoice = SalesInvoice::create([
                'invoice_no' => SalesInvoice::generateInvoiceNo(),
                'customer_id' => $request->customer_id,
                'invoice_date' => now()->toDateString(),
                'grand_total' => collect($items)->sum('line_total'),
                'notes' => $request->notes,
                'status' => 'finalized',
                'created_by' => auth()->user()->name ?? 'System',
            ]);

            // Load customer relationship for payment prompt
            $invoice->load('customer');

            $lowStockAlerts = [];

            foreach ($items as $index => $item) {
                SalesInvoiceItem::create([
                    'sales_invoice_id' => $invoice->id,
                    'line_no' => $index + 1,
                    'item_id' => $item['item_id'],
                    'item_name' => $item['item_name'],
                    'qty' => $item['qty'],
                    'unit_price' => $item['unit_price'],
                    'discount' => $item['discount'],
                    'line_total' => $item['line_total'],
                ]);

                // Reduce stock for finalized invoice
                Stock::reduce($item['item_id'], $item['qty']);

                // Check reorder level after reduction
                $alert = $this->buildReorderAlert($item['item_id'], $item['item_name']);
                if ($alert) {
                    $lowStockAlerts[] = $alert;
                }
            }

            DB::commit();
            session()->forget($this->sessionKey());

            return response()->json([
                'success' => true,
                'message' => 'Invoice finalized successfully',
                'invoice_id' => $invoice->id,
                'pdf_url' => route('sales_invoices.pdf', $invoice->id),
                'redirect_url' => route('sales_invoices.index'),
                'prompt_payment' => true,
                'payment_data' => [
                    'invoice_id' => $invoice->id,
                    'invoice_no' => $invoice->invoice_no,
                    'customer_id' => $invoice->customer_id,
                    'customer_name' => $invoice->customer->name ?? 'Unknown Customer',
                    'grand_total' => $invoice->grand_total,
                    'outstanding_amount' => $invoice->getOutstandingAmount()
                ],
                'low_stock_alerts' => $lowStockAlerts,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error finalizing invoice: ' . $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        try {
            $invoice = SalesInvoice::with(['customer', 'items'])->findOrFail($id);

            // Check if user is authenticated
            if (!auth()->check()) {
                return redirect()->route('login')->with('error', 'Please login to continue');
            }

            $user = auth()->user();

            // Allow editing for hold invoices OR finalized invoices for admin/manager
            if ($invoice->status === 'hold') {
                // Hold invoices can be edited by anyone with access
            } elseif ($invoice->status === 'finalized' && in_array($user->usertype ?? 'user', ['admin', 'manager'])) {
                // Finalized invoices can only be edited by admin/manager
            } else {
                return redirect()->route('sales_invoices.index')
                               ->with('error', 'You do not have permission to edit this invoice');
            }
        } catch (\Exception $e) {
            return redirect()->route('sales_invoices.index')
                           ->with('error', 'Invoice not found or error occurred: ' . $e->getMessage());
        }

        try {
            // Load items into session for editing
            $items = $invoice->items->map(function($item) use ($invoice) {
                // For finalized invoices, get current stock + already used quantity
                $stockQty = 0;
                try {
                    if ($invoice->status === 'finalized') {
                        $stock = Stock::where('item_ID', $item->item_id)->first();
                        $stockQty = ($stock ? $stock->quantity : 0) + $item->qty; // Add back the used quantity
                    } else {
                        $stock = Stock::where('item_ID', $item->item_id)->first();
                        $stockQty = $stock ? $stock->quantity : 0;
                    }
                } catch (\Exception $e) {
                    // If stock lookup fails, default to 0
                    $stockQty = 0;
                }

                return [
                    'item_id' => $item->item_id,
                    'item_name' => $item->item_name,
                    'unit_price' => $item->unit_price,
                    'qty' => $item->qty,
                    'discount' => $item->discount,
                    'line_total' => $item->line_total,
                    'stock_qty' => $stockQty,
                ];
            })->toArray();

            $sessionKey = $this->sessionKey();
            session([$sessionKey => $items]);

            return view('sales_invoices.edit', compact('invoice'));
        } catch (\Exception $e) {
            return redirect()->route('sales_invoices.index')
                           ->with('error', 'Error loading invoice items: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $invoice = SalesInvoice::with('items')->findOrFail($id);

        // Check permissions
        if ($invoice->status === 'hold') {
            // Hold invoices can be updated by anyone with access
        } elseif ($invoice->status === 'finalized' && in_array(auth()->user()->usertype, ['admin', 'manager'])) {
            // Finalized invoices can only be updated by admin/manager
        } else {
            return response()->json(['success' => false, 'message' => 'You do not have permission to update this invoice']);
        }

        $request->validate([
            'customer_id' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $items = session()->get($this->sessionKey(), []);
        if (empty($items)) {
            return response()->json(['success' => false, 'message' => 'No items added']);
        }

        DB::beginTransaction();
        try {
            // If invoice is finalized, restore stock before making changes
            if ($invoice->status === 'finalized') {
                foreach ($invoice->items as $existingItem) {
                    // Add back the stock that was previously deducted
                    $stock = Stock::where('item_ID', $existingItem->item_id)->first();
                    if ($stock) {
                        $stock->increment('quantity', $existingItem->qty);
                    }
                }
            }

            // Check stock availability for new items (if invoice is finalized)
            if ($invoice->status === 'finalized') {
                foreach ($items as $item) {
                    $stock = Stock::where('item_ID', $item['item_id'])->first();
                    $availableQty = $stock ? $stock->quantity : 0;

                    if ($item['qty'] > $availableQty) {
                        DB::rollBack();
                        return response()->json([
                            'success' => false,
                            'message' => "Insufficient stock for {$item['item_name']}. Available: {$availableQty}"
                        ]);
                    }
                }
            }

            // Delete existing items
            $invoice->items()->delete();

            // Update invoice
            $invoice->update([
                'customer_id' => $request->customer_id,
                'grand_total' => collect($items)->sum('line_total'),
                'notes' => $request->notes,
            ]);

            // Add new items
            $lowStockAlerts = [];
            foreach ($items as $index => $item) {
                SalesInvoiceItem::create([
                    'sales_invoice_id' => $invoice->id,
                    'line_no' => $index + 1,
                    'item_id' => $item['item_id'],
                    'item_name' => $item['item_name'],
                    'qty' => $item['qty'],
                    'unit_price' => $item['unit_price'],
                    'discount' => $item['discount'],
                    'line_total' => $item['line_total'],
                ]);

                // If invoice is finalized, deduct stock for new items
                if ($invoice->status === 'finalized') {
                    Stock::reduce($item['item_id'], $item['qty']);
                    $alert = $this->buildReorderAlert($item['item_id'], $item['item_name']);
                    if ($alert) {
                        $lowStockAlerts[] = $alert;
                    }
                }
            }

            DB::commit();
            session()->forget($this->sessionKey());

            $message = $invoice->status === 'finalized'
                ? 'Finalized invoice updated successfully'
                : 'Invoice updated successfully';

            return response()->json([
                'success' => true,
                'message' => $message,
                'redirect_url' => route('sales_invoices.index'),
                'low_stock_alerts' => $lowStockAlerts,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error updating invoice: ' . $e->getMessage()]);
        }
    }

    public function finalizeHold($id)
    {
        $invoice = SalesInvoice::with('items')->findOrFail($id);

        if ($invoice->status !== 'hold') {
            return redirect()->route('sales_invoices.index')
                           ->with('error', 'Only hold invoices can be finalized');
        }

        DB::beginTransaction();
        try {
            // Check stock availability for all items
            foreach ($invoice->items as $item) {
                $stock = Stock::where('item_ID', $item->item_id)->first();
                $availableQty = $stock ? $stock->quantity : 0;

                if ($item->qty > $availableQty) {
                    return redirect()->back()
                                   ->with('error', "Insufficient stock for {$item->item_name}. Available: {$availableQty}");
                }
            }

            // Reduce stock for all items
            $lowStockAlerts = [];
            foreach ($invoice->items as $item) {
                Stock::reduce($item->item_id, $item->qty);
                $alert = $this->buildReorderAlert($item->item_id, $item->item_name);
                if ($alert) {
                    $lowStockAlerts[] = $alert;
                }
            }

            // Update invoice status
            $invoice->update(['status' => 'finalized']);

            DB::commit();

            $redirect = redirect()->route('sales_invoices.index')
                           ->with('success', 'Invoice finalized successfully');
            if (!empty($lowStockAlerts)) {
                $redirect->with('low_stock_alerts', $lowStockAlerts);
            }
            return $redirect;

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                           ->with('error', 'Error finalizing invoice: ' . $e->getMessage());
        }
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
            // Use explicit reorder level from item table
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

    public function pdf($id)
    {
        $invoice = SalesInvoice::with(['customer', 'items'])->findOrFail($id);

        $pdf = Pdf::loadView('sales_invoices.pdf', compact('invoice'));

        // Open PDF in new tab instead of forcing download
        return $pdf->stream("invoice-{$invoice->invoice_no}.pdf");
    }

    public function show($id)
    {
        $invoice = SalesInvoice::with(['customer', 'items'])->findOrFail($id);
        return view('sales_invoices.show', compact('invoice'));
    }

    public function destroy($id)
    {
        $invoice = SalesInvoice::with('items')->findOrFail($id);

        // Check permissions for deleting finalized invoices
        if ($invoice->status === 'finalized' && !in_array(auth()->user()->usertype ?? 'user', ['admin', 'manager'])) {
            return redirect()->back()
                           ->with('error', 'You do not have permission to delete finalized invoices');
        }

        DB::beginTransaction();
        try {
            $stockRestorations = [];

            // If invoice is finalized, restore stock before deleting
            if ($invoice->status === 'finalized') {
                foreach ($invoice->items as $item) {
                    $stock = Stock::where('item_ID', $item->item_id)->first();
                    if ($stock) {
                        $oldQty = $stock->quantity;
                        $stock->increment('quantity', $item->qty);
                        $stockRestorations[] = [
                            'item_id' => $item->item_id,
                            'item_name' => $item->item_name,
                            'restored_qty' => $item->qty,
                            'old_stock' => $oldQty,
                            'new_stock' => $stock->quantity
                        ];
                    }
                }

                // Log stock restorations
                \Log::info('Stock restored during invoice deletion', [
                    'invoice_id' => $invoice->id,
                    'invoice_no' => $invoice->invoice_no,
                    'restorations' => $stockRestorations
                ]);
            }

            $invoice->delete();

            DB::commit();

            $message = $invoice->status === 'finalized'
                ? 'Finalized invoice deleted successfully and stock restored for ' . count($stockRestorations) . ' items'
                : 'Invoice deleted successfully';

            return redirect()->route('sales_invoices.index')
                           ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error deleting invoice', [
                'invoice_id' => $id,
                'error' => $e->getMessage()
            ]);
            return redirect()->back()
                           ->with('error', 'Error deleting invoice: ' . $e->getMessage());
        }
    }

    public function emailInvoice(Request $request, $id)
    {
        $invoice = SalesInvoice::with(['customer', 'items'])->findOrFail($id);

        // Check if customer has email
        if (!$invoice->customer->email) {
            return response()->json([
                'success' => false,
                'message' => 'Customer email address is not available'
            ]);
        }

        try {
            // Send email with PDF attachment
            Mail::to($invoice->customer->email)
                ->queue(new InvoiceMail($invoice));

            return response()->json([
                'success' => true,
                'message' => "Invoice emailed successfully to {$invoice->customer->email}"
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send email: ' . $e->getMessage()
            ]);
        }
    }

    public function getSessionItems()
    {
        $sessionKey = $this->sessionKey();
        $items = session()->get($sessionKey, []);
        $total = collect($items)->sum('line_total');

        return response()->json([
            'success' => true,
            'items' => $items,
            'total' => $total
        ]);
    }

    public function createPayment(Request $request)
    {
        $request->validate([
            'invoice_id' => 'required|exists:sales_invoices,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'bank_account_id' => 'nullable|exists:bank_accounts,id',
            'payment_category_id' => 'required|exists:payment_categories,id',
            'description' => 'nullable|string|max:255',
            'reference_no' => 'nullable|string|max:100',
        ]);

        try {
            $invoice = \App\Models\SalesInvoice::with('customer')->findOrFail($request->invoice_id);
            $outstandingAmount = $invoice->getOutstandingAmount();

            if ($request->amount > $outstandingAmount) {
                return response()->json([
                    'success' => false,
                    'message' => "Payment amount ({$request->amount}) cannot exceed outstanding amount ({$outstandingAmount})"
                ]);
            }

            DB::beginTransaction();

            $transaction = \App\Models\PaymentTransaction::create([
                'transaction_no' => \App\Models\PaymentTransaction::generateTransactionNumber(),
                'type' => 'cash_in',
                'amount' => $request->amount,
                'transaction_date' => now(),
                'description' => $request->description ?: "Payment for Invoice {$invoice->invoice_no}",
                'payment_method_id' => $request->payment_method_id,
                'bank_account_id' => $request->bank_account_id,
                'payment_category_id' => $request->payment_category_id,
                'customer_custom_id' => $invoice->customer_id,
                'sales_invoice_id' => $invoice->id,
                'reference_no' => $request->reference_no,
                'status' => 'completed',
                'created_by' => auth()->user()->name ?? 'System',
                'approved_by' => auth()->user()->name ?? 'System',
                'approved_at' => now(),
            ]);

            // Update bank account balance if specified
            if ($request->bank_account_id) {
                $bankAccount = \App\Models\BankAccount::find($request->bank_account_id);
                if ($bankAccount) {
                    $bankAccount->increment('current_balance', $request->amount);
                }
            }

            DB::commit();

            $remainingBalance = $outstandingAmount - $request->amount;

            return response()->json([
                'success' => true,
                'message' => 'Payment recorded successfully!',
                'payment_id' => $transaction->id,
                'transaction_no' => $transaction->transaction_no,
                'remaining_balance' => $remainingBalance,
                'is_fully_paid' => $remainingBalance <= 0
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error recording payment: ' . $e->getMessage()
            ]);
        }
    }
}
