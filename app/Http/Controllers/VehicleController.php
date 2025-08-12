<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\Customer;
use App\Models\VehicleBrand;
use App\Models\VehicleRoute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class VehicleController extends Controller
{
    public function approve(Request $request, Vehicle $vehicle)
    {
        $vehicle->update([
            'is_approved' => true,
            'approved_by' => auth()->user()->name ?? auth()->user()->email,
            'approved_at' => now(),
        ]);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Vehicle approved.']);
        }

        return back()->with('success', 'Vehicle approved successfully.');
    }
    public function index(Request $request)
    {
        $query = Vehicle::with(['customer', 'brand', 'route', 'serviceSchedule']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('vehicle_no', 'like', "%$search%")
                    ->orWhereHas('customer', function ($c) use ($search) {
                        $c->where(function ($sub) use ($search) {
                            $sub->where('name', 'like', "%$search%")
                                ->orWhere('phone', 'like', "%$search%")
                                ->orWhere('nic', 'like', "%$search%");
                        });
                    });
            });
        }

        $vehicles = $query->latest()->paginate(10)->withQueryString();

        if ($request->ajax()) {
            return view('vehicles.table', compact('vehicles'))->render();
        }

        return view('vehicles.index', compact('vehicles'));
    }

    public function create()
    {
        $brands = Cache::remember('active_vehicle_brands', 60 * 60, function () {
        return VehicleBrand::where('status', true)->orderBy('name')->get();
    });

    $routes = Cache::remember('active_vehicle_routes', 60 * 60, function () {
        return VehicleRoute::where('status', true)->orderBy('name')->get();
    });

    return view('vehicles.create', compact('brands', 'routes'));
    }

    public function store(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'customer_id' => 'required|exists:customers,id',
            'vehicle_no' => 'required|string|unique:vehicles,vehicle_no|regex:/^[A-Z]{2,3}-\d{4}$/',
            'brand_id' => 'required|exists:vehicle_brands,id',
            'model' => 'required|string',
            'engine_no' => 'required|alpha_num',
            'chassis_no' => 'required|alpha_num',
            'route_id' => 'required|exists:vehicle_routes,id',
            'year_of_manufacture' => 'required|digits:4',
            'date_of_purchase' => 'required|date',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        Vehicle::create([
            ...$request->only([
                'customer_id',
                'vehicle_no',
                'brand_id',
                'model',
                'engine_no',
                'chassis_no',
                'route_id',
                'year_of_manufacture',
                'date_of_purchase',
            ]),
            'registration_status' => $request->boolean('registration_status'),
            'status' => true,
        ]);

        return redirect()->route('vehicles.index')->with('success', 'Vehicle registered.');
    }

    public function show(Vehicle $vehicle)
    {
        $vehicle->load(['customer', 'brand', 'route']);
        return view('vehicles.show', compact('vehicle'));
    }

    public function edit(Vehicle $vehicle)
    {
        $brands = VehicleBrand::where('status', true)->get();
        $routes = VehicleRoute::where('status', true)->get();

        return view('vehicles.edit', compact('vehicle', 'brands', 'routes'));
    }

    public function update(Request $request, Vehicle $vehicle)
    {
        $validator = \Validator::make($request->all(), [
            'vehicle_no' => 'required|regex:/^[A-Z]{2,3}-\d{4}$/|unique:vehicles,vehicle_no,' . $vehicle->id,
            'brand_id' => 'required|exists:vehicle_brands,id',
            'model' => 'required|string',
            'engine_no' => 'required|alpha_num',
            'chassis_no' => 'required|alpha_num',
            'route_id' => 'required|exists:vehicle_routes,id',
            'year_of_manufacture' => 'required|digits:4',
            'date_of_purchase' => 'required|date',
        ]);
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        $vehicle->update([
            ...$request->only([
                'vehicle_no',
                'brand_id',
                'model',
                'engine_no',
                'chassis_no',
                'route_id',
                'year_of_manufacture',
                'date_of_purchase',
            ]),
            'registration_status' => $request->boolean('registration_status'),
        ]);

        return redirect()->route('vehicles.index')->with('success', 'Vehicle updated.');
    }

    public function destroy(Vehicle $vehicle)
    {
        $vehicle->update(['status' => false]);
        return back()->with('success', 'Vehicle deactivated.');
    }

    public function customerSearch(Request $request)
    {
        $q = $request->get('q', '');
        return Customer::where('status', true)
            ->where(function ($query) use ($q) {
                $query->where('name', 'like', "%$q%")
                    ->orWhere('phone', 'like', "%$q%")
                    ->orWhere('nic', 'like', "%$q%");
            })
            ->limit(10)
            ->get()
            ->map(fn($c) => [
                'id' => $c->id,
                'text' => "{$c->name} ({$c->phone})",
            ]);
    }

    public function checkDuplicate(Request $request)
    {
        $vehicleNo = $request->get('vehicle_no');
        $exists = Vehicle::where('vehicle_no', $vehicleNo)->exists();

        return response()->json(['exists' => $exists]);
    }




}
