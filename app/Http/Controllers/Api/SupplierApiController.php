<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class SupplierApiController extends Controller
{
    public function list(Request $request)
    {
        $suppliers = DB::table('suppliers')
            ->select('Supp_CustomID as id', 'Supp_Name as text')
            ->where('status', true)
            ->limit(20)
            ->get();
        return response()->json($suppliers);
    }
}
