<?php

namespace App\Http\Controllers;

use App\Models\GRN;
use App\Models\GRNItem;
use App\Models\Products;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Barryvdh\DomPDF\Facade\Pdf;


class GRNController extends Controller
{
    protected function sessionKey(): string
    {
        return 'temp_grn_items_' . auth()->id();
    }

    public function index()
    {
        $grns = GRN::where('status', 1)
            ->orderByDesc('grn_id')
            ->paginate(10);

        return view('grns.index', compact('grns'));
    }

    public function create()
    {
        session()->forget($this->sessionKey());
        return view('grns.create');
    }

    public function storeTempItem(Request $request)
    {
        $request->validate([
            'item_id' => 'required|string',
            'qty' => 'required|integer|min:1',
        ]);
        $item = Products::where('item_ID', $request->item_id)->first();
        if (!$item) {
            return response()->json(['success' => false, 'message' => 'Invalid item']);
        }

        $key = $this->sessionKey();
        $items = session()->get($key, []);

        // Prevent duplicates: merge if exists
        foreach ($items as &$existing) {
            if ($existing['item_ID'] === $item->item_ID) {
                $existing['qty'] += $request->qty;
                $existing['line_total'] = $existing['qty'] * $existing['price'];
                session([$key => $items]);
                return response()->json(['success' => true, 'items' => $items]);
            }
        }

        $items[] = [
            'item_ID' => $item->item_ID,
            'description' => $item->item_Name,
            'price' => $item->sales_Price,
            'qty' => $request->qty,
            'line_total' => $request->qty * $item->sales_Price,
        ];

        session([$key => $items]);

        return response()->json(['success' => true, 'items' => $items]);
    }

    public function removeTempItem(Request $request)
    {
        $request->validate(['index' => 'required|integer|min:0']);
        $key = $this->sessionKey();
        $items = session()->get($key, []);
        if (isset($items[$request->index])) {
            unset($items[$request->index]);
            session([$key => array_values($items)]);
        }

        return response()->json(['success' => true, 'items' => session($key)]);
    }

    public function fetchTempItems()
    {
        return response()->json([
            'success' => true,
            'items' => session()->get($this->sessionKey(), [])
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'grn_date' => 'required|date',
            'supp_Cus_ID' => 'required|string',
        ]);

        $items = session()->get($this->sessionKey(), []);
        if (empty($items)) {
            return back()->with('error', 'Add at least one item to GRN.');
        }

        DB::beginTransaction();

        try {
            $grnNo = GRN::generateGRNNumber();

            $grnId = GRN::insertGetId([
                'grn_no' => $grnNo,
                'grn_date' => $request->grn_date,
                'po_Auto_ID' => $request->po_Auto_ID,
                'po_No' => $request->po_No,
                'supp_Cus_ID' => $request->supp_Cus_ID,
                'invoice_no' => $request->invoice_no,
                'invoice_date' => $request->invoice_date,
                'received_by' => $request->received_by,
                'note' => $request->note,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            foreach ($items as $item) {
                GRNItem::create([
                    'grn_id' => $grnId,
                    'item_ID' => $item['item_ID'],
                    'item_Name' => $item['description'],
                    'qty_received' => $item['qty'],
                    'price' => $item['price'],
                    'line_total' => $item['line_total'],
                ]);

                // Update stock
                Stock::increase($item['item_ID'], $item['qty']);

                // Update item price if GRN price is higher
                Products::updatePriceIfHigher($item['item_ID'], $item['price']);
            }

            session()->forget($this->sessionKey());

            DB::commit();
            return redirect()->route('grns.index')->with('success', 'GRN created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create GRN: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $grn = GRN::findOrFail($id);

        $items = GRNItem::where('grn_id', $id)->get();

        $tempItems = [];
        foreach ($items as $item) {
            $tempItems[] = [
                'item_ID' => $item->item_ID,
                'description' => $item->item_Name,
                'price' => $item->price,
                'qty' => $item->qty_received,
                'line_total' => $item->line_total,
            ];
        }

        session([$this->sessionKey() => $tempItems]);

        return view('grns.edit', compact('grn'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'grn_date' => 'required|date',
            'supp_Cus_ID' => 'required|string',
        ]);

        $items = session()->get($this->sessionKey(), []);
        if (empty($items)) {
            return back()->with('error', 'No items to save. Please add at least one.');
        }

        DB::beginTransaction();

        try {
            $grn = GRN::findOrFail($id);

            // Rollback old stock
            $oldItems = GRNItem::where('grn_id', $id)->get();
            foreach ($oldItems as $item) {
                $currentStock = Stock::where('item_ID', $item->item_ID)->value('quantity');
                if ($currentStock < $item->qty_received) {
                    return back()->with('error', "Not enough stock to rollback for item {$item->item_ID}.");
                }
            }

            foreach ($oldItems as $item) {
                Stock::decrease($item->item_ID, $item->qty_received);
            }

            GRNItem::where('grn_id', $id)->delete();

            // Save updated items
            foreach ($items as $item) {
                GRNItem::create([
                    'grn_id' => $id,
                    'item_ID' => $item['item_ID'],
                    'item_Name' => $item['description'],
                    'qty_received' => $item['qty'],
                    'price' => $item['price'],
                    'line_total' => $item['line_total'],
                ]);

                Stock::increase($item['item_ID'], $item['qty']);
                Products::updatePriceIfHigher($item['item_ID'], $item['price']);
            }

            // Update GRN master
            $grn->update([
                'grn_date' => $request->grn_date,
                'po_Auto_ID' => $request->po_Auto_ID,
                'po_No' => $request->po_No,
                'supp_Cus_ID' => $request->supp_Cus_ID,
                'invoice_no' => $request->invoice_no,
                'invoice_date' => $request->invoice_date,
                'received_by' => $request->received_by,
                'note' => $request->note,
                'updated_at' => now(),
            ]);

            session()->forget($this->sessionKey());
            DB::commit();

            return redirect()->route('grns.index')->with('success', 'GRN updated.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update GRN: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $grn = GRN::findOrFail($id);

        if (!$grn->status) {
            return back()->with('error', 'GRN already deleted.');
        }

        $items = GRNItem::where('grn_id', $id)->get();

        foreach ($items as $item) {
            $currentStock = Stock::where('item_ID', $item->item_ID)->value('quantity');
            if ($currentStock < $item->qty_received) {
                return back()->with('error', "Cannot delete. Not enough stock to reverse for item {$item->item_ID}.");
            }
        }

        foreach ($items as $item) {
            Stock::decrease($item->item_ID, $item->qty_received);
        }

        $grn->update(['status' => false]);

        return back()->with('success', 'GRN deleted and stock rolled back.');
    }


    public function pdf($id)
    {
        $grn = GRN::findOrFail($id);
        $items = GRNItem::where('grn_id', $id)->get();
        $supplier = DB::table('suppliers')->where('Supp_CustomID', $grn->supp_Cus_ID)->first();
        $pdf = Pdf::loadView('grns.pdf', compact('grn', 'items', 'supplier'));
        return $pdf->stream("GRN-{$grn->grn_no}.pdf");
    }
}
