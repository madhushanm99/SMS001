<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\Http\Request;

class PublicVehicleController extends Controller
{
    public function show(Request $request, string $vehicleNo)
    {
        $vehicleNo = strtoupper($vehicleNo);

        $vehicle = Vehicle::query()
            ->with(['brand', 'serviceSchedule'])
            ->where('vehicle_no', $vehicleNo)
            ->first();

        $schedule = $vehicle?->serviceSchedule;

        return view('public.vehicle_service', [
            'vehicle' => $vehicle,
            'schedule' => $schedule,
        ]);
    }
}
