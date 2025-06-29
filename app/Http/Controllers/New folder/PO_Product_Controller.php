<?php

// app/Http/Controllers/ProductController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Products;
use App\Models\Po;
use App\Models\Po_Item;
use App\Models\Supplier;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class PO_Product_Controller extends Controller
{
    // Autocomplete Product ID
    public function autocomplete(Request $request)
    {
        $query = $request->get('query');
        $products = Products::where('item_ID', 'LIKE', "%{$query}%")->pluck('item_ID');
        return response()->json($products);
    }

    // Get Product Details by Product ID
    public function getProductDetails($item_ID)
    {
        $product = Products::where('item_ID', $item_ID)->first();
        if ($product) {
            return response()->json([
                'item_Name' => $product->item_Name,
                'sales_Price' => $product->sales_Price,
            ]);
        }
        return response()->json(['error' => 'Product not found'], 404);
    }

    // Add Product Line to Session
    public function addProductLine(Request $request)
    {
        $request->validate([
            'item_ID' => 'required|exists:products,item_ID',
            'qty' => 'required|integer|min:1',
        ]);

        $product = Products::where('item_ID', $request->item_ID)->first();

        $line = [
            'line_no' => 1,
            'item_ID' => $product->item_ID,
            'item_Name' => $product->item_Name,
            'sales_Price' => $product->sales_Price,
            'qty' => $request->qty,
            'line_total' => number_format($product->sales_Price * $request->qty, 2, '.', ''),
        ];

        $lines = session()->get('product_lines', []);
        $line['line_no'] = count($lines) + 1;
        $lines[] = $line;
        session(['product_lines' => $lines]);

        return response()->json(['success' => true, 'lines' => $lines]);
    }

    // Get all product lines from session
    public function getProductLines()
    {
        $lines = session('product_lines', []);
        return response()->json($lines);
    }

}
