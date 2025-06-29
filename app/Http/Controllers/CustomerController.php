<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;


class CustomerController extends Controller
{

    public function index(Request $request)
    {
        $query = Customer::query();
        // Filter by search term
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('phone', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%")
                    ->orWhere('nic', 'like', "%$search%");
            });
        }

        // Filter by registration date range
        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        $customers = $query->latest()->paginate(10)->withQueryString();

        // For real-time filter rendering
        if ($request->ajax()) {
            return view('customers.table', compact('customers'))->render();
        }

        return view('customers.index', compact('customers'));
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'name' => 'required|string|min:2',
            'phone' => 'nullable|regex:/^0\d{9}$/|unique:customers,phone',
            'email' => 'nullable|email|unique:customers,email',
            'nic' => 'nullable|regex:/^\d{9}[VXvx]$/|unique:customers,nic',
        ]);

        // if ($validator->fails()) {
        //     // return redirect()->back()->withErrors($validator)->withInput();
        //     return redirect()->back()->with('error', $error)->withInput();

        // }
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $customId = Customer::generateCustomID();

        Customer::create([
            'custom_id' => $customId,
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'nic' => $request->nic,
            'group_name' => $request->group_name ?: 'All Groups',
            'address' => $request->address,
            'status' => true,
        ]);

        return redirect()->route('customers.index')->with('success', 'Customer created.');
    }

    public function show($id)
    {
        $customer = Customer::findOrFail($id);
        return view('customers.show', compact('customer'));
    }

    public function edit($id)
    {
        $customer = Customer::findOrFail($id);
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);

        $validator = \Validator::make($request->all(), [
            'name' => 'required|string|min:2',
            'phone' => 'nullable|regex:/^0\d{9}$/',
            'email' => 'nullable|email',
            'nic' => 'nullable|regex:/^\d{9}[VXvx]$/',
        ]);

        if ($validator->fails()) {
            // return redirect()->back()->withErrors($validator)->withInput();
            return redirect()->back()->with('error', "Vallidation error")->withInput();

        }

        $customer->update([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'nic' => $request->nic,
            'group_name' => $request->group_name,
            'address' => $request->address,
        ]);

        return redirect()->route('customers.index')->with('success', 'Customer updated.');
    }

    public function destroy($id)
    {
        $customer = Customer::findOrFail($id);

        // Soft deactivate instead of deleting
        $customer->update(['status' => false]);

        return back()->with('success', 'Customer marked as inactive.');
    }



}
