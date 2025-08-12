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
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use Laravel\Pail\ValueObjects\Origin\Console;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class PO_Controller extends Controller
{
    public function index(Request $request)
    {
        Session::forget('temp_po_items');
        $query = DB::table('po')
            ->leftJoin('suppliers', 'suppliers.Supp_CustomID', '=', 'po.supp_Cus_ID')
            ->where('po.status', 1)
            ->select('po.*', 'suppliers.Supp_Name as supplier_name');
        ;

        if ($request->filled('supplier')) {
            $query->where('po.supp_Cus_ID', $request->supplier);
        }

        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->whereBetween('po.po_date', [$request->from_date, $request->to_date]);
        }

        $purchaseOrders = $query->orderByDesc('po_Auto_ID')->paginate(10);
        $suppliers = DB::table('suppliers')->get();

        return view('purchase_orders.index', compact('purchaseOrders', 'suppliers'));
    }


    public function create()
    {
        $suppliers = DB::table('suppliers')->where('status', true)->get();
        Session::put('po_temp_items', []);
        return view('purchase_orders.create', compact('suppliers'));
    }



    public function searchItems(Request $request)
    {
        $search = trim($request->input('q'));
        $results = [];


        if ($search !== '') {
            $cacheKey = 'search_items:' . strtolower($search);
            $items = Cache::get($cacheKey);


            if (!$items) {

                $items = DB::table('item')
                    ->select('item_ID', 'item_Name', 'sales_Price','catagory_Name')
                    ->where('item_ID', 'like', "%$search%")
                    ->orWhere('item_Name', 'like', "%$search%")
                    ->limit(10)
                    ->get()
                    ->toArray();


                Cache::put($cacheKey, $items, now()->addMinutes(5));
            }


            foreach ($items as $item) {
                $itemId = $item->item_ID;
                Redis::zincrby('search_popularity', 1, $itemId);
                Redis::hset('item_details', $itemId, json_encode([
                    'id' => $itemId,
                    'text' => "{$itemId} - {$item->item_Name}",
                    'price' => $item->sales_Price,
                    'desc' => $item->catagory_Name,
                ]));
            }


            $results = collect($items)->map(function ($item) {
                return [
                    'id' => $item->item_ID,
                    'text' => "{$item->item_ID} - {$item->item_Name}",
                    'price' => $item->sales_Price,
                    'desc' => $item->catagory_Name,
                ];
            });
        } else {

            $popularIds = Redis::zrevrange('search_popularity', 0, 9);
            foreach ($popularIds as $id) {
                $itemJson = Redis::hget('item_details', $id);
                if ($itemJson) {
                    $results[] = json_decode($itemJson, true);
                }
            }
        }

        return response()->json($results);
    }



    public function storeTempItem(Request $request)
    {
        logger('fuck');
        logger(session('temp_po_items'));
        Log::info('Adding temp item: ' . $request->item_id);
        $request->validate([
            'item_id' => 'required',
            'qty' => 'required|integer|min:1',
        ]);


        $item = DB::table('item')->where('item_ID', $request->item_id)->first();
        if (!$item) {
            return response()->json(['success' => false, 'message' => 'Item not found']);
        }


        $tempItems = session()->get('temp_po_items', []);


        $found = false;
        foreach ($tempItems as &$existingItem) {
            if ($existingItem['item_ID'] == $request->item_id) {
                $existingItem['qty'] += $request->qty;
                $existingItem['line_total'] = $existingItem['price'] * $existingItem['qty'];
                $found = true;
                break;
            }
        }

        if (!$found) {
            $tempItems[] = [
                'item_ID' => $item->item_ID,
                'description' => $item->item_Name,
                'price' => $item->sales_Price,
                'qty' => $request->qty,
                'line_total' => $item->sales_Price * $request->qty,
            ];
        }


        session(['temp_po_items' => $tempItems]);

        return response()->json(['success' => true, 'items' => $tempItems]);
    }

    public function removeTempItem(Request $request)
    {
        $request->validate([
            'index' => 'required|integer|min:0',
        ]);

        $tempItems = session()->get('temp_po_items', []);

        if (isset($tempItems[$request->index])) {
            unset($tempItems[$request->index]);
            $tempItems = array_values($tempItems);
            session(['temp_po_items' => $tempItems]);
        }

        return response()->json(['success' => true, 'items' => $tempItems]);
    }

    public function getItemDetails($itemId)
    {
        $item = DB::table('item')->where('item_ID', $itemId)->first();
        return response()->json($item);
    }

    public function store(Request $request)
    {
        $request->validate([
            'po_date' => 'required|date',
            'supp_Cus_ID' => 'required|string',
        ]);

        $tempItems = Session::get('temp_po_items', []);
        if (count($tempItems) == 0) {
            return back()->with('error', 'No items added to purchase order');
        }

        DB::beginTransaction();
        try {
            $po_No = PO::generatePONumber();

            $poId = DB::table('po')->insertGetId([
                'po_No' => $po_No,
                'po_date' => $request->po_date,
                'supp_Cus_ID' => $request->supp_Cus_ID,
                'grand_Total' => collect($tempItems)->sum('line_total'),
                'note' => $request->note,
                'Reff_No' => $request->Reff_No,
                'emp_Name' => $request->emp_Name,
                'orderStatus' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            foreach ($tempItems as $index => $item) {
                DB::table('po__Item')->insert([
                    'po_Auto_ID' => $poId,
                    'po_No' => $po_No,
                    'list_No' => $index + 1,
                    'item_ID' => $item['item_ID'],
                    'qty' => $item['qty'],
                    'price' => $item['price'],
                    'line_Total' => $item['line_total'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            Session::forget('temp_po_items');
            DB::commit();
            return redirect()->route('purchase_orders.index')->with('success', 'Purchase Order created successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $purchaseOrder = DB::table('po')->where('po_Auto_ID', $id)->first();

        if (!$purchaseOrder)
            abort(404);

        if (in_array($purchaseOrder->orderStatus, ['approved', 'received'])) {
            return back()->with('error', 'Approved or Received POs cannot be edited.');
        }


        $poItems = DB::table('po__Item')->where('po_Auto_ID', $id)->get();
        if (!session()->has('temp_po_items')) {
            $tempItems = [];
            foreach ($poItems as $item) {

                $itemDetails = DB::table('item')->where('item_ID', $item->item_ID)->first();

                $tempItems[] = [
                    'item_ID' => $item->item_ID,
                    'description' => $itemDetails ? $itemDetails->item_Name : '',
                    'price' => $item->price,
                    'qty' => $item->qty,
                    'line_total' => $item->line_Total,
                ];
            }

            session(['temp_po_items' => $tempItems]);
        }

        $suppliers = DB::table('suppliers')->get();
        return view('purchase_orders.edit', compact('purchaseOrder', 'suppliers'));
    }

    public function fetchTempItems()
    {
        $items = session()->get('temp_po_items', []);
        return response()->json(['success' => true, 'items' => $items]);
    }

    public function update(Request $request, $id)
    {
        // dd(session('temp_po_items'));

        $request->validate([
            'po_date' => 'required|date',
            'supp_Cus_ID' => 'required|string',
        ]);

        $items = session()->get('temp_po_items', []);
        if (empty($items))
            return back()->with('error', 'Add at least one item.');

        $grandTotal = array_sum(array_column($items, 'line_total'));

        DB::beginTransaction();

        try {
            DB::table('po')->where('po_Auto_ID', $id)->update([
                'po_date' => $request->po_date,
                'supp_Cus_ID' => $request->supp_Cus_ID,
                'grand_Total' => $grandTotal,
                'note' => $request->note,
                'Reff_No' => $request->Reff_No,
                'emp_Name' => $request->emp_Name,
                'updated_at' => now(),
            ]);

            DB::table('po__Item')->where('po_Auto_ID', $id)->delete();

            $poNo = DB::table('po')->where('po_Auto_ID', $id)->value('po_No');

            foreach ($items as $index => $item) {
                DB::table('po__Item')->insert([
                    'po_Auto_ID' => $id,
                    'po_No' => $poNo,
                    'list_No' => $index + 1,
                    'item_ID' => $item['item_ID'],
                    'qty' => $item['qty'],
                    'price' => $item['price'],
                    'line_Total' => $item['line_total'],
                    'created_at' => now(),
                    'updated_at' => now(),

                ]);
            }
            logger(session('temp_po_items'));

            DB::commit();
            session()->forget('temp_po_items');
            return redirect()->route('purchase_orders.index')->with('success', 'PO updated!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Update failed: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        DB::table('po')->where('po_Auto_ID', $id)->update(['status' => 0]);
        return redirect()->route('purchase_orders.index')->with('success', 'Purchase Order deleted successfully');
    }

    public function exportPdf($id)
    {
        $po = DB::table('po')->where('po_Auto_ID', $id)->first();
        $items = DB::table('po__Item')
            ->join('item', 'po__Item.item_ID', '=', 'item.item_ID')
            ->where('po__Item.po_Auto_ID', $id)
            ->select(
                'po__Item.*',
                'item.item_Name as item_name'
            )
            ->get();

        $supplier = DB::table('suppliers')->where('Supp_CustomID', $po->supp_Cus_ID)->first();

        $pdf = Pdf::loadView('purchase_orders.pdf', compact('po', 'items', 'supplier'));
        return $pdf->stream("PO-{$po->po_No}.pdf");
    }

    public function changeStatus(Request $request, $id)
    {
        // Allow only managers and admin to change PO status
        if (!auth()->check() || !in_array(auth()->user()->usertype, ['manager', 'admin'])) {
            return back()->with('error', 'You do not have permission to change purchase order status.');
        }

        $request->validate([
            'status' => 'required|in:draft,pending,approved,received,cancelled',
        ]);

        DB::table('po')->where('po_Auto_ID', $id)->update([
            'orderStatus' => $request->status,
            'updated_at' => now(),
        ]);

        // If status changed to approved, email PO to supplier (queued)
        if ($request->status === 'approved') {
            $po = DB::table('po')->where('po_Auto_ID', $id)->first();
            $supplier = DB::table('suppliers')->where('Supp_CustomID', $po->supp_Cus_ID)->first();

            if ($supplier && !empty($supplier->Email)) {
                try {
                    \Mail::to($supplier->Email)->queue(new \App\Mail\PurchaseOrderApprovedMail($po, $supplier));
                } catch (\Throwable $e) {
                    // Fail silently but notify user
                    return back()->with('success', 'Status updated to Approved, but failed to queue email: ' . $e->getMessage());
                }
                return back()->with('success', 'Status updated to Approved and PO emailed to supplier (queued).');
            } else {
                return back()->with('success', 'Status updated to Approved. Note: Supplier has no email on file.');
            }
        }

        return back()->with('success', 'Status updated!');
    }

    public function resetSearchPopularity()
{
    Redis::del('search_popularity');

    return response()->json([
        'status' => 'success',
        'message' => 'Search popularity reset!'
    ]);
}
}


