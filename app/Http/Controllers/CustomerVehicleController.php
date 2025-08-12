<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\VehicleBrand;
use App\Models\VehicleRoute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Writer;

class CustomerVehicleController extends Controller
{
    /**
     * Display a listing of customer's vehicles.
     */
    public function index()
    {
        $customer = Auth::guard('customer')->user()->customer;
        $vehicles = $customer->vehicles()->with(['brand', 'route', 'serviceSchedule'])->latest()->get();

        return view('customer.vehicles.index', compact('vehicles'));
    }

    /**
     * Show the form for creating a new vehicle.
     */
    public function create()
    {
        $brands = Cache::remember('active_vehicle_brands', 60 * 60, function () {
            return VehicleBrand::where('status', true)->orderBy('name')->get();
        });

        $routes = Cache::remember('active_vehicle_routes', 60 * 60, function () {
            return VehicleRoute::where('status', true)->orderBy('name')->get();
        });

        return view('customer.vehicles.create', compact('brands', 'routes'));
    }

    /**
     * Store a newly created vehicle in storage.
     */
    public function store(Request $request)
    {
        $customer = Auth::guard('customer')->user()->customer;

        $validator = \Validator::make($request->all(), [
            'vehicle_no' => 'required|string|unique:vehicles,vehicle_no|regex:/^[A-Z]{2,3}-\d{4}$/',
            'brand_id' => 'required|exists:vehicle_brands,id',
            'model' => 'required|string|max:255',
            'engine_no' => 'required|string|max:255',
            'chassis_no' => 'required|string|max:255',
            'route_id' => 'required|exists:vehicle_routes,id',
            'year_of_manufacture' => 'required|digits:4|max:' . (date('Y') + 1),
            'date_of_purchase' => 'required|date|before_or_equal:today',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        Vehicle::create([
            'customer_id' => $customer->id,
            'vehicle_no' => strtoupper($request->vehicle_no),
            'brand_id' => $request->brand_id,
            'model' => $request->model,
            'engine_no' => strtoupper($request->engine_no),
            'chassis_no' => strtoupper($request->chassis_no),
            'route_id' => $request->route_id,
            'year_of_manufacture' => $request->year_of_manufacture,
            'date_of_purchase' => $request->date_of_purchase,
            'registration_status' => $request->boolean('registration_status'),
            'status' => true,
        ]);

        return redirect()->route('customer.vehicles.index')
            ->with('success', 'Vehicle registered successfully!');
    }

    /**
     * Display the specified vehicle.
     */
    public function show(Vehicle $vehicle)
    {
        $customer = Auth::guard('customer')->user()->customer;

        // Ensure the vehicle belongs to the authenticated customer
        if ($vehicle->customer_id !== $customer->id) {
            abort(403, 'Unauthorized access to vehicle.');
        }

        $vehicle->load(['brand', 'route', 'serviceSchedule']);

        return view('customer.vehicles.show', compact('vehicle'));
    }

    /**
     * Show the form for editing the specified vehicle.
     */
    public function edit(Vehicle $vehicle)
    {
        $customer = Auth::guard('customer')->user()->customer;

        // Ensure the vehicle belongs to the authenticated customer
        if ($vehicle->customer_id !== $customer->id) {
            abort(403, 'Unauthorized access to vehicle.');
        }

        $brands = VehicleBrand::where('status', true)->orderBy('name')->get();
        $routes = VehicleRoute::where('status', true)->orderBy('name')->get();

        return view('customer.vehicles.edit', compact('vehicle', 'brands', 'routes'));
    }

    /**
     * Update the specified vehicle in storage.
     */
    public function update(Request $request, Vehicle $vehicle)
    {
        $customer = Auth::guard('customer')->user()->customer;

        // Ensure the vehicle belongs to the authenticated customer
        if ($vehicle->customer_id !== $customer->id) {
            abort(403, 'Unauthorized access to vehicle.');
        }

        $validator = \Validator::make($request->all(), [
            'vehicle_no' => 'required|regex:/^[A-Z]{2,3}-\d{4}$/|unique:vehicles,vehicle_no,' . $vehicle->id,
            'brand_id' => 'required|exists:vehicle_brands,id',
            'model' => 'required|string|max:255',
            'engine_no' => 'required|string|max:255',
            'chassis_no' => 'required|string|max:255',
            'route_id' => 'required|exists:vehicle_routes,id',
            'year_of_manufacture' => 'required|digits:4|min:1900|max:' . (date('Y') + 1),
            'date_of_purchase' => 'required|date|before_or_equal:today',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $vehicle->update([
            'vehicle_no' => strtoupper($request->vehicle_no),
            'brand_id' => $request->brand_id,
            'model' => $request->model,
            'engine_no' => strtoupper($request->engine_no),
            'chassis_no' => strtoupper($request->chassis_no),
            'route_id' => $request->route_id,
            'year_of_manufacture' => $request->year_of_manufacture,
            'date_of_purchase' => $request->date_of_purchase,
            'registration_status' => $request->boolean('registration_status'),
        ]);

        return redirect()->route('customer.vehicles.index')
            ->with('success', 'Vehicle updated successfully!');
    }

    /**
     * Check if vehicle number is available
     */
    public function checkAvailability(Request $request)
    {
        $vehicleNo = $request->get('vehicle_no');
        $vehicleId = $request->get('vehicle_id');

        $query = Vehicle::where('vehicle_no', $vehicleNo);

        if ($vehicleId) {
            $query->where('id', '!=', $vehicleId);
        }

        $exists = $query->exists();

        return response()->json(['available' => !$exists]);
    }

    public function downloadQr(Vehicle $vehicle)
    {
        $customer = Auth::guard('customer')->user()->customer;
        if ($vehicle->customer_id !== $customer->id) {
            abort(403);
        }

        $url = route('public.vehicle.show', ['vehicleNo' => $vehicle->vehicle_no]);

        $renderer = new ImageRenderer(
            new RendererStyle(300),
            new SvgImageBackEnd()
        );
        $writer = new Writer($renderer);
        $svg = $writer->writeString($url);

        $filename = 'vehicle-' . $vehicle->vehicle_no . '-qr.svg';
        return response($svg, 200, [
            'Content-Type' => 'image/svg+xml',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"'
        ]);
    }
}
