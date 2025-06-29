<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class ItemApiController extends Controller
{
    public function search(Request $request)
    {
        $term = $request->input('term', '');
        $items = DB::table('item')
            ->where('item_ID', 'like', "%$term%")
            ->orWhere('item_Name', 'like', "%$term%")
            ->limit(10)
            ->get();
        return response()->json($items->map(function ($item) {
            return [
                'id' => $item->item_ID,
                'text' => "{$item->item_ID} - {$item->item_Name}",
                'price' => $item->sales_Price,
            ];
        }));
    }
}
