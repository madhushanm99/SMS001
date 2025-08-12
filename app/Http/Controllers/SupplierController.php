<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Supplier;
use App\Models\SupplierGroup;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $query = Supplier::select('Supp_ID', 'Supp_CustomID', 'Supp_Name', 'Address1', 'Company_Name', 'Phone', 'Fax', 'Email')->where('status', 1);
        ;

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('Supp_Name', 'LIKE', "%{$search}%")
                    ->orWhere('Supp_CustomID', 'LIKE', "%{$search}%")
                    ->orWhere('Company_Name', 'LIKE', "%{$search}%")
                    ->orWhere('Phone', 'LIKE', "%{$search}%")
                    ->orWhere('Email', 'LIKE', "%{$search}%");
            });
        }

        $suppliers = $query->paginate(10);
        $suppliers->appends(['search' => $search]);

        if ($request->ajax()) {
            // Return only the table partial for AJAX requests
            return view('Purchase.partials.suppliers_table', compact('suppliers'))->render();
        }

        // For normal requests, return full page
        return view('Purchase.suppliers', compact('suppliers', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Assuming you have a SupplierGroup model for the groups dropdown
        $groups = SupplierGroup::all();
        return view('Purchase/suppliers_create', compact('groups'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'Supp_CustomID' => 'nullable|string|max:255',
            'Supp_Name' => 'required|string|max:255',
            'Company_Name' => 'nullable|string|max:255',
            'Phone' => 'nullable|string|max:50',
            'Address1' => 'nullable|string|max:255',
            'Email' => 'nullable|email|max:255',
            'Web' => 'nullable|url|max:255',
            'Fax' => 'nullable|string|max:50',
            'Supp_Group_Name' => 'nullable|string|max:255',
            'Remark' => 'nullable|string',
        ]);

        $supplier = Supplier::create($validated);

        return redirect()->route('suppliers.show', $supplier->Supp_ID)
            ->with('success', 'Supplier created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $supplier = Supplier::findOrFail($id);

        // Load recent GRNs with basic info and related payments for the purchase history tab
        $purchaseHistory = \App\Models\GRN::with(['items', 'paymentTransactions' => function($q) {
            $q->where('status', 'completed');
        }])
        ->where('supp_Cus_ID', $supplier->Supp_CustomID)
        ->orderByDesc('grn_date')
        ->orderByDesc('grn_id')
        ->limit(20)
        ->get();

        return view('Purchase.supplier_show', compact('supplier', 'purchaseHistory'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Supplier $supplier)
    {
        $groups = SupplierGroup::all();
        return view('Purchase.supplier_edit', compact('supplier', 'groups'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'Supp_CustomID' => 'nullable|string|max:255',
            'Supp_Name' => 'required|string|max:255',
            'Company_Name' => 'nullable|string|max:255',
            'Phone' => 'nullable|string|max:50',
            'Fax' => 'nullable|string|max:50',
            'Email' => 'nullable|email|max:255',
            'Web' => 'nullable|url|max:255',
            'Address1' => 'nullable|string|max:500',
            'Supp_Group_Name' => 'nullable|string|max:255',
            'Remark' => 'nullable|string|max:1000',
            'Last_GRN' => 'nullable|string|max:255',
            'Total_Orders' => 'nullable|numeric|min:0',
        ]);

        $supplier->update($validated);

        return redirect()->route('suppliers.show', $supplier->Supp_ID)
            ->with('success', 'Supplier Edited successfully!');
    }


    //* Remove the specified resource from storage.


    public function destroy($id)
    {
        $supplier = Supplier::findOrFail($id); // Find the supplier or fail
        $supplier->status = 0;
        $supplier->save();
        return redirect()->route('suppliers')->with('success', 'Supplier deleted successfully!');
    }
}
