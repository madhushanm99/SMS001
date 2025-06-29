<?php

namespace App\Http\Controllers;

use App\Models\Po;
use App\Models\Po_Item;
use App\Models\Supplier;

use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;

class PurchaseOderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('Purchase.purchaseOrder');

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('Purchase.purchaseOrder_create');

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function SupAutocomplete(Request $request)
    {
        $query = $request->get('query');
        $suppliers = Supplier::where('Supp_Name', 'LIKE', "%{$query}%")
            ->orWhere('Company_Name', 'LIKE', "%{$query}%")
            ->orWhere('Supp_CustomID', 'LIKE', "%{$query}%")
            ->select('Supp_ID', 'Supp_CustomID', 'Supp_Name', 'Company_Name')
            ->get();

        return response()->json($suppliers);
    }

    // Get Supplier Details
    public function getDetails($Supp_ID)
    {
        $supplier = Supplier::find($Supp_ID);
        if ($supplier) {
            return response()->json([
                'Supp_CustomID' => $supplier->Supp_CustomID,
                'Supp_Name' => $supplier->Supp_Name,
                'Company_Name' => $supplier->Company_Name,
                'Phone' => $supplier->Phone,
                'Address1' => $supplier->Address1,
            ]);
        }
        return response()->json(['error' => 'Supplier not found'], 404);
    }

    public function createPurchaseOrder(Request $request)
    {
        // Validate
        $request->validate([
            'supp_Cus_ID' => 'required|exists:suppliers,Supp_CustomID'
        ]);

        DB::beginTransaction();

        try {
            // Create PO
            $po = Po::create([
                'po_No' => 'PO-' . time(),
                'po_date' => now(),
                'supp_Cus_ID' => $request->supp_Cus_ID, // Matches Supp_CustomID
                'grand_Total' => $request->grand_Total,
                'note' => $request->note,
                'Reff_No' => $request->Reff_No,
                'emp_Name' => auth()->user()->name,
                'status' => '1'
            ]);

            // Add PO Items (from session)
            $lines = session('product_lines', []);
            foreach ($lines as $line) {
                Po_Item::create([
                    'po_No' => $po->po_No,
                    'list_No' => $line['line_no'],
                    'item_ID' => $line['item_ID'],
                    'qty' => $line['qty'],
                    'price' => $line['sales_Price'],
                    'line_Total' => $line['line_total']
                ]);
            }

            DB::commit();
            session()->forget('product_lines');
             return response()->json(['success' => true, 'po_No' => $po->po_No]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
