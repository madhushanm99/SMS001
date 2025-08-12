<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Products;
use App\Models\Item_Location;
use App\Models\Item_Stock;


class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $query = Products::select('item_ID_Auto', 'item_ID', 'item_Name', 'product_Type', 'catagory_Name', 'sales_Price', 'units', 'reorder_level', 'unitofMeture', 'location')->where('status', 1);
        ;

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('item_ID', 'LIKE', "%{$search}%")
                    ->orWhere('item_Name', 'LIKE', "%{$search}%");
            });
        }

        $items = $query->paginate(10);
        $items->appends(['search' => $search]);

        if ($request->ajax()) {
            // Return only the table partial for AJAX requests
            return view('Purchase.partials.items_table', compact('items'))->render();
        }

        // For normal requests, return full page
        return view('Purchase.products', compact('items', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        // Assuming you have a SupplierGroup model for the groups dropdown
        $locations = Item_Location::all();
        return view('Purchase/product_create', compact('locations'));

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'item_ID'         => 'required|string|max:255|unique:item,item_ID',
            'item_Name'       => 'required|string|max:255',
            'product_Type'    => 'required|in:Genuine,After Marcket',
            'catagory_Name'   => 'required|in:Spare,Oil,Electric,Modify',
            'sales_Price'     => 'nullable|numeric|min:0',
            'units'           => 'required|integer|min:0',
            'reorder_level'   => 'nullable|integer|min:0',
            'unitofMeture'    => 'required|in:Item,ml,g,L,KG',
            'location'        => 'required|exists:Item_Location,location_Name',

        ]);
        $item = Products::create($validated);

        return redirect()->route('products', $item)
            ->with('success', 'Item created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $item = Products::findOrFail($id);

        return view('Purchase.product_show', compact('item'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Products $product)
    {
        $locations = Item_Location::all();
        return view('Purchase.product_edit', compact('product', 'locations'));

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Products $product)
    {
        $validated = $request->validate([
            'item_ID'         => 'required|string|max:255',
            'item_Name'       => 'required|string|max:255',
            'product_Type'    => 'required|in:Genuine,After Marcket',
            'catagory_Name'   => 'required|in:Spare,Oil,Electric,Modify',
            'sales_Price'     => 'nullable|numeric|min:0',
            'units'           => 'required|integer|min:0',
            'reorder_level'   => 'nullable|integer|min:0',
            'unitofMeture'    => 'required|in:Item,ml,gL,KG',
            'location'        => 'required|exists:Item_Location,location_Name',

        ]);
        $product->update($validated);
        return redirect()->route('products.show', $product->item_ID_Auto )
            ->with('success', 'Item Edited successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $product = Products::findOrFail($id);
        $product->status = 0;
        $product->save();
        return redirect()->route('products')->with('success', 'Item deleted successfully!');
    }
}
