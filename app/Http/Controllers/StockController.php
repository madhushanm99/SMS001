<?php

namespace App\Http\Controllers;
use App\Models\Products;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $query = Products::select(
            'item.item_ID',
            'item.item_Name',
            'item.sales_Price',
            'item.reorder_level as reorder_level',
            DB::raw('COALESCE(stock.quantity, 0) as qty'),
            DB::raw('COALESCE(stock.cost_value, 0) as cost_value')
        )
            ->leftJoin('stock', 'item.item_ID', '=', 'stock.item_ID')
            ->where('item.status', true);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('item.item_ID', 'like', "%$search%")
                    ->orWhere('item.item_Name', 'like', "%$search%");
            });
        }

        $items = $query->orderBy('item.item_Name')->paginate(10)->withQueryString();

        return view('stock.index', compact('items', 'search'));

    }
}
