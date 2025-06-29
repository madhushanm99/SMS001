<?php

namespace App\Http\Controllers;

use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnItem;
use App\Models\GRN;
use App\Models\GRNItem;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class PurchaseReturnController extends Controller
{
    public function index(Request $request)
    {
        $query = PurchaseReturn::query();
        if ($request->filled('supplier')) {
            $query->where('supp_Cus_ID', $request->supplier);
        }

        if ($request->filled('grn')) {
            $query->where('grn_no', $request->grn);
        }

        $returns = $query->latest()->get();
        $suppliers = DB::table('suppliers')->get();
        $grns = DB::table('grn')->select('grn_no')->get();

        return view('purchase_returns.index', compact('returns', 'suppliers', 'grns'));
    }
    public function create()
    {
        $grns = GRN::where('status', true)->get();
        $grn_items_by_grn_id = [];

        foreach ($grns as $grn) {
            $items = GRNItem::where('grn_id', $grn->grn_id)->get()->map(function ($item) {
                $stockQty = Stock::where('item_ID', $item->item_ID)->value('quantity') ?? 0;
                return [
                    'item_ID' => $item->item_ID,
                    'item_Name' => $item->item_Name,
                    'qty_received' => $item->qty_received,
                    'price' => $item->price,
                    'stock_qty' => $stockQty,
                ];
            });

            $grn_items_by_grn_id[$grn->grn_id] = $items;
        }

        return view('purchase_returns.create', compact('grns', 'grn_items_by_grn_id'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'grn_id' => 'required|exists:grn,grn_id',
            'supp_Cus_ID' => 'required',
            'items' => 'required|array|min:1',
            'items.*.item_ID' => 'required|string',
            'items.*.qty_returned' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric',
        ]);

        DB::beginTransaction();
        try {
            // Stock validation
            foreach ($request->items as $item) {
                $stock = Stock::where('item_ID', $item['item_ID'])->value('quantity') ?? 0;
                if ($item['qty_returned'] > $stock) {
                    return back()->with('error', "Cannot return {$item['item_ID']}, not enough stock.");
                }
            }

            $grn = GRN::findOrFail($request->grn_id);

            $return = PurchaseReturn::create([
                'return_no' => PurchaseReturn::generateReturnNo(),
                'grn_id' => $grn->grn_id,
                'grn_no' => $grn->grn_no,
                'supp_Cus_ID' => $request->supp_Cus_ID,
                'note' => $request->note,
                'returned_by' => auth()->user()->name ?? 'system',
                'status' => true,
            ]);

            foreach ($request->items as $item) {
                PurchaseReturnItem::create([
                    'purchase_return_id' => $return->id,
                    'item_ID' => $item['item_ID'],
                    'item_Name' => $item['item_Name'],
                    'qty_returned' => $item['qty_returned'],
                    'price' => $item['price'],
                    'line_total' => $item['qty_returned'] * $item['price'],
                    'reason' => $item['reason'] ?? null,
                ]);

                // Reduce stock
                Stock::where('item_ID', $item['item_ID'])->decrement('quantity', $item['qty_returned']);
            }

            DB::commit();
            return redirect()->route('purchase_returns.index')->with('success', 'Purchase return saved.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Error saving return: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $pr = PurchaseReturn::findOrFail($id);
        if (!$pr->status) {
            return back()->with('error', 'Already deleted.');
        }

        $pr->update(['status' => false]);
        return back()->with('success', 'Return soft-deleted. Stock not restored.');
    }

    public function pdf($id)
    {
        $return = PurchaseReturn::with('items')->findOrFail($id);
        $supplier = DB::table('suppliers')->where('Supp_CustomID', $return->supp_Cus_ID)->first();
        $pdf = Pdf::loadView('purchase_returns.pdf', compact('return', 'supplier'));
        return $pdf->stream("PR-{$return->return_no}.pdf");
    }

}
