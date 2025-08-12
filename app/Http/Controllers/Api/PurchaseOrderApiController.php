<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class PurchaseOrderApiController extends Controller
{
    public function list(Request $request)
    {
        $term = $request->input('term', '');
        $purchaseOrders = DB::table('po')
            ->where('po_No', 'like', "%$term%")
            ->limit(10)
            ->get();

        return response()->json($purchaseOrders->map(function ($po) {
            return [
                'id' => $po->po_No,
                'text' => $po->po_No,
                'supplier_id' => $po->supp_Cus_ID
            ];
        }));
    }

    public function byNumber(Request $request)
    {
        $poNo = $request->query('po_no');
        if (!$poNo) {
            return response()->json(['message' => 'po_no is required'], 422);
        }
        $po = DB::table('po')->where('po_No', $poNo)->first();
        if (!$po) {
            return response()->json(['message' => 'PO not found'], 404);
        }
        return response()->json($po);
    }
}
