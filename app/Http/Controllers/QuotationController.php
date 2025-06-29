<?php

namespace App\Http\Controllers;

use App\Models\Quotation;
use App\Models\QuotationItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class QuotationController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('quotations')
            ->leftJoin('customers', 'quotations.customer_custom_id', '=', 'customers.custom_id')
            ->select('quotations.*', 'customers.name as customer_name', 'customers.phone', 'customers.nic');
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('quotations.quotation_no', 'like', "%$search%")
                    ->orWhere('quotations.vehicle_no', 'like', "%$search%")
                    ->orWhere('customers.name', 'like', "%$search%")
                    ->orWhere('customers.phone', 'like', "%$search%")
                    ->orWhere('customers.nic', 'like', "%$search%");
            });
        }
        if ($request->filled('from')) {
            $query->whereDate('quotations.quotation_date', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('quotations.quotation_date', '<=', $request->to);
        }

        $quotations = $query->orderByDesc('quotations.created_at')->paginate(10)->withQueryString();

        if ($request->ajax()) {
            return view('quotations.table', compact('quotations'))->render();
        }

        return view('quotations.index', compact('quotations'));
    }
    public function create()
    {
        session()->forget('quotation_items'); // clear previous temp
        return view('quotations.create');
    }

    public function addTempItem(Request $request)
    {
        $request->validate([
            'type' => 'required|in:spare,job',
            'item_id' => 'required|string',
            'description' => 'required|string',
            'qty' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0'
        ]);

        $item = [
            'type' => $request->type,
            'item_id' => $request->item_id,
            'description' => $request->description,
            'qty' => $request->qty,
            'price' => $request->price,
            'line_total' => $request->qty * $request->price
        ];

        $items = session()->get('quotation_items', []);
        $items[] = $item;
        session(['quotation_items' => $items]);

        return response()->json(['success' => true, 'items' => $items]);

    }

    public function removeTempItem(Request $request)
    {
        $request->validate(['index' => 'required|integer']);

        $items = session()->get('quotation_items', []);
        if (isset($items[$request->index])) {
            array_splice($items, $request->index, 1);
            session(['quotation_items' => $items]);
        }

        return response()->json(['success' => true, 'items' => $items]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_custom_id' => 'required|string',
            'vehicle_no' => 'nullable|string'
        ]);

        $items = session()->get('quotation_items', []);
        if (empty($items)) {
            return response()->json(['success' => false, 'message' => 'Add at least one item']);
        }

        DB::beginTransaction();
        try {
            $quotation = Quotation::create([
                'quotation_no' => Quotation::generateQuotationNo(),
                'customer_custom_id' => $request->customer_custom_id,
                'vehicle_no' => $request->vehicle_no,
                'quotation_date' => now()->toDateString(),
                'grand_total' => collect($items)->sum('line_total'),
                'created_by' => auth()->user()->name ?? 'System',
                'status' => true,
            ]);

            foreach ($items as $i => $item) {
                QuotationItem::create([
                    'quotation_id' => $quotation->id,
                    'line_no' => $i + 1,
                    'item_type' => $item['type'],
                    'item_id' => $item['item_id'],
                    'description' => $item['description'],
                    'qty' => $item['qty'],
                    'price' => $item['price'],
                    'line_total' => $item['line_total'],
                    'status' => true,
                ]);
            }

            DB::commit();
            session()->forget('quotation_items');
            return response()->json([
                'success' => true,
                'quotation_id' => $quotation->id,
                'quotation_no' => $quotation->quotation_no,
                'pdf_url' => route('quotations.pdf', $quotation->id),
                'redirect_url' => route('quotations.index')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed: ' . $e->getMessage()]);
        }
    }

    public function searchCustomers(Request $request)
    {
        $q = $request->input('term', '');
        return response()->json(
            DB::table('customers')
                ->where('status', true)
                ->where(function ($query) use ($q) {
                    $query->where('name', 'like', "%$q%")
                        ->orWhere('nic', 'like', "%$q%")
                        ->orWhere('phone', 'like', "%$q%");
                })
                ->limit(10)
                ->get()
                ->map(fn($c) => [
                    'id' => $c->custom_id,
                    'text' => "{$c->name} ({$c->phone})",
                ])
        );
    }

    public function searchVehicles(Request $request)
    {
        $q = $request->input('q', '');
        $customer_id = $request->input('customer_id');

        return DB::table('vehicles')
            ->where('status', true)
            ->where('customer_id', function ($query) use ($customer_id) {
                $query->select('id')->from('customers')->where('custom_id', $customer_id);
            })
            ->where('vehicle_no', 'like', "%$q%")
            ->limit(10)
            ->get()
            ->map(fn($v) => ['id' => $v->vehicle_no, 'text' => $v->vehicle_no]);
    }

    public function searchItems(Request $request)
    {
        $q = $request->input('q', '');

        return DB::table('item')
            ->where('status', true)
            ->where(function ($query) use ($q) {
                $query->where('item_ID', 'like', "%$q%")
                    ->orWhere('item_Name', 'like', "%$q%");
            })
            ->limit(10)
            ->get()
            ->map(fn($i) => [
                'id' => $i->item_ID,
                'text' => "{$i->item_ID} - {$i->item_Name}",
                'price' => $i->sales_Price,
            ]);
    }

    public function searchJobs(Request $request)
    {
        $q = $request->input('q', '');

        return DB::table('job_types')
            ->where('status', true)
            ->where('jobType', 'like', "%$q%")
            ->limit(10)
            ->get()
            ->map(fn($j) => [
                'id' => $j->jobCustomID,
                'text' => $j->jobType,
                'price' => $j->salesPrice,
            ]);
    }
    public function edit(Quotation $quotation)
    {
        $quotation->load('items');
        return view('quotations.edit', compact('quotation'));
    }

    public function update(Request $request, Quotation $quotation)
    {

        return redirect()->route('quotations.index')->with('success', 'Quotation updated.');
    }

    public function removeItem(Quotation $quotation, QuotationItem $item)
    {
        if ($item->quotation_id !== $quotation->id) {
            return back()->with('error', 'Item mismatch.');
        }
        $item->delete();
        $quotation->update(['grand_total' => $quotation->items()->sum('line_total')]);

        return back()->with('success', 'Item removed.');
    }

    public function destroy(Quotation $quotation_id)
    {

        $quotation_id->update(['status' => false]);

        return back()->with('success', 'Job Type marked as inactive.' . $quotation_id);
    }


    public function pdf(Quotation $quotation)
    {
        $quotation->load('items');
        $pdf = Pdf::loadView('quotations.pdf', [
            'quotation' => $quotation,
        ]);

        return $pdf->stream("quotation_{$quotation->quotation_no}.pdf");

    }
}
