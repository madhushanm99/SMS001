<?php
// app/Http/Controllers/PurchaseOrderController.php

namespace App\Http\Controllers;

use App\Models\Po;
use App\Models\Po_Item;
use App\Models\Products;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class PO_Controller extends Controller
{
    public function index()
    {
        $purchaseOrders = Po::orderBy('created_at', 'desc')
            ->paginate(10);

        return view('purchase_orders.index', compact('purchaseOrders'));
    }

    public function create()
    {
        $suppliers = Supplier::orderBy('name')->get();
        $poNumber = Po::generatePONumber();

        // Clear any existing temporary items
        Session::forget('temp_po_items');

        return view('purchase_orders.create', compact('suppliers', 'poNumber'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'order_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $tempItems = Session::get('temp_po_items', []);

        if (empty($tempItems)) {
            return back()->withErrors(['items' => 'Please add at least one item to the purchase order.']);
        }

        DB::transaction(function () use ($request, $tempItems) {
            $purchaseOrder = Po::create([
                'po_number' => Po::generatePONumber(),
                'supplier_id' => $request->supplier_id,
                'order_date' => $request->order_date,
                'notes' => $request->notes,
                'status' => 'draft'
            ]);

            $totalAmount = 0;
            foreach ($tempItems as $item) {
                Po_Item::create([
                    'purchase_order_id' => $purchaseOrder->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'line_total' => $item['line_total']
                ]);
                $totalAmount += $item['line_total'];
            }

            $purchaseOrder->update(['total_amount' => $totalAmount]);
        });

        // Clear temporary items
        Session::forget('temp_po_items');

        return redirect()->route('purchase-orders.index')
            ->with('success', 'Purchase order created successfully.');
    }

    public function show(Po $purchaseOrder)
    {
        $purchaseOrder->load(['supplier', 'items.product']);
        return view('purchase_orders.show', compact('purchaseOrder'));
    }

    public function edit(Po $purchaseOrder)
    {
        $suppliers = Supplier::orderBy('name')->get();
        $purchaseOrder->load(['items.product']);

        // Store current items in session for editing
        $tempItems = [];
        foreach ($purchaseOrder->items as $item) {
            $tempItems[] = [
                'product_id' => $item->product_id,
                'product_name' => $item->product->name,
                'product_description' => $item->product->description,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'line_total' => $item->line_total
            ];
        }
        Session::put('temp_po_items', $tempItems);

        return view('purchase_orders.edit', compact('purchaseOrder', 'suppliers'));
    }

    public function update(Request $request, Po $purchaseOrder)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'order_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $tempItems = Session::get('temp_po_items', []);

        if (empty($tempItems)) {
            return back()->withErrors(['items' => 'Please add at least one item to the purchase order.']);
        }

        DB::transaction(function () use ($request, $purchaseOrder, $tempItems) {
            $purchaseOrder->update([
                'supplier_id' => $request->supplier_id,
                'order_date' => $request->order_date,
                'notes' => $request->notes,
            ]);

            // Delete existing items
            $purchaseOrder->items()->delete();

            // Add new items
            $totalAmount = 0;
            foreach ($tempItems as $item) {
                Po::create([
                    'purchase_order_id' => $purchaseOrder->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'line_total' => $item['line_total']
                ]);
                $totalAmount += $item['line_total'];
            }

            $purchaseOrder->update(['total_amount' => $totalAmount]);
        });

        Session::forget('temp_po_items');

        return redirect()->route('purchase-orders.index')
            ->with('success', 'Purchase order updated successfully.');
    }

    public function destroy(Po $purchaseOrder)
    {
        $purchaseOrder->delete();
        return redirect()->route('purchase-orders.index')
            ->with('success', 'Purchase order deleted successfully.');
    }

    // AJAX methods for handling temporary items
    public function getProduct(Request $request)
    {
        $product = Products::find($request->product_id);

        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        return response()->json([
            'id' => $product->id,
            'name' => $product->name,
            'description' => $product->description,
            'price' => $product->price
        ]);
    }

    public function addTempItem(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0'
        ]);

        $product = Products::find($request->product_id);
        $quantity = $request->quantity;
        $unitPrice = $request->unit_price;
        $lineTotal = $quantity * $unitPrice;

        $tempItems = Session::get('temp_po_items', []);

        // Check if product already exists, if so update it
        $productExists = false;
        foreach ($tempItems as $key => $item) {
            if ($item['product_id'] == $request->product_id) {
                $tempItems[$key] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_description' => $product->description,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'line_total' => $lineTotal
                ];
                $productExists = true;
                break;
            }
        }

        if (!$productExists) {
            $tempItems[] = [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'product_description' => $product->description,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'line_total' => $lineTotal
            ];
        }

        Session::put('temp_po_items', $tempItems);

        return response()->json(['success' => true, 'items' => $tempItems]);
    }

    public function removeTempItem(Request $request)
    {
        $tempItems = Session::get('temp_po_items', []);
        $productId = $request->product_id;

        $tempItems = array_filter($tempItems, function($item) use ($productId) {
            return $item['product_id'] != $productId;
        });

        $tempItems = array_values($tempItems); // Re-index array

        Session::put('temp_po_items', $tempItems);

        return response()->json(['success' => true, 'items' => $tempItems]);
    }

    public function getTempItems()
    {
        $tempItems = Session::get('temp_po_items', []);
        return response()->json(['items' => $tempItems]);
    }
}
