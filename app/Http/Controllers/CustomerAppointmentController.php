<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Notification;
use App\Mail\AppointmentCreatedMail;
use App\Mail\AppointmentCancelledMail;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class CustomerAppointmentController extends Controller
{
    /**
     * Display a listing of customer's appointments.
     */
    public function index(Request $request)
    {
        $customer = Auth::guard('customer')->user()->customer;

        $query = $customer->appointments()->with(['vehicle']);

        // Filter by status if provided
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by service type if provided
        if ($request->filled('service_type')) {
            $query->where('service_type', $request->service_type);
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('appointment_no', 'like', "%$search%")
                  ->orWhere('vehicle_no', 'like', "%$search%")
                  ->orWhere('customer_notes', 'like', "%$search%");
            });
        }

        $appointments = $query->latest('appointment_date')->paginate(10)->withQueryString();

        // Get customer's vehicles for filter dropdown
        $customerVehicles = $customer->vehicles()->where('status', true)->get();

        // Calculate summary statistics
        $totalAppointments = $customer->appointments()->count();
        $pendingAppointments = $customer->appointments()->pending()->count();
        $confirmedAppointments = $customer->appointments()->confirmed()->count();
        $nextAppointment = $customer->appointments()
            ->whereIn('status', ['pending', 'confirmed'])
            ->where('appointment_date', '>=', now()->toDateString())
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->first();

        return view('customer.appointments.index', compact(
            'appointments',
            'customerVehicles',
            'totalAppointments',
            'pendingAppointments',
            'confirmedAppointments',
            'nextAppointment'
        ));
    }

    /**
     * Show the form for creating a new appointment.
     */
    public function create()
    {
        $customer = Auth::guard('customer')->user()->customer;
        $vehicles = $customer->vehicles()->where('status', true)->get();

        if ($vehicles->isEmpty()) {
            return redirect()->route('customer.vehicles.create')
                ->with('info', 'Please register a vehicle first before booking an appointment.');
        }

        return view('customer.appointments.create', compact('vehicles'));
    }

    /**
     * Get available time slots for a specific date via AJAX.
     */
    public function getAvailableSlots(Request $request)
    {
        $request->validate([
            'date' => 'required|date|after:today',
        ]);

        $date = Carbon::parse($request->date);

        // Skip weekends (optional - adjust based on business hours)
        if ($date->isWeekend()) {
            return response()->json([
                'success' => false,
                'message' => 'Appointments are not available on weekends.'
            ]);
        }

        $availableSlots = Appointment::getAvailableTimeSlots($date);

        return response()->json([
            'success' => true,
            'slots' => $availableSlots
        ]);
    }

    /**
     * Store a newly created appointment.
     */
    public function store(Request $request)
    {
        $customer = Auth::guard('customer')->user()->customer;

        $request->validate([
            'vehicle_no' => 'required|string',
            'service_type' => 'required|in:NS,FS',
            'appointment_date' => 'required|date|after:today',
            'appointment_time' => 'required|string',
            'customer_notes' => 'nullable|string|max:1000',
        ]);

        // Verify the vehicle belongs to the customer
        $vehicle = $customer->vehicles()->where('vehicle_no', $request->vehicle_no)->first();
        if (!$vehicle) {
            return redirect()->back()
                ->withErrors(['vehicle_no' => 'Selected vehicle does not belong to you.'])
                ->withInput();
        }

        // Check if the time slot is still available
        $existingAppointment = Appointment::where('appointment_date', $request->appointment_date)
            ->where('appointment_time', $request->appointment_time)
            ->whereIn('status', ['pending', 'confirmed'])
            ->exists();

        if ($existingAppointment) {
            return redirect()->back()
                ->withErrors(['appointment_time' => 'This time slot is no longer available.'])
                ->withInput();
        }

        // Create the appointment
        $appointment = Appointment::create([
            'appointment_no' => Appointment::generateAppointmentNo(),
            'customer_id' => $customer->custom_id,
            'vehicle_no' => $request->vehicle_no,
            'service_type' => $request->service_type,
            'appointment_date' => $request->appointment_date,
            'appointment_time' => $request->appointment_time,
            'customer_notes' => $request->customer_notes,
            'status' => 'pending',
        ]);

        // Send real-time notification to staff
        NotificationService::appointmentCreated($appointment);

        // Send confirmation email to customer
        try {
            Mail::to($customer->email)->queue(new AppointmentCreatedMail($appointment));
        } catch (\Exception $e) {
            \Log::error('Failed to send appointment created email', [
                'error' => $e->getMessage(),
                'appointment_id' => $appointment->id,
                'customer_email' => $customer->email,
            ]);
        }

        return redirect()->route('customer.appointments.index')
            ->with('success', 'Appointment request submitted successfully! You will receive a confirmation email once approved.');
    }

    /**
     * Display the specified appointment.
     */
    public function show(Appointment $appointment)
    {
        $customer = Auth::guard('customer')->user()->customer;

        // Ensure the appointment belongs to the authenticated customer
        if ($appointment->customer_id !== $customer->custom_id) {
            abort(403, 'Unauthorized access to appointment.');
        }

        $appointment->load(['vehicle']);

        return view('customer.appointments.show', compact('appointment'));
    }

    /**
     * Cancel the specified appointment.
     */
    public function cancel(Appointment $appointment)
    {
        $customer = Auth::guard('customer')->user()->customer;

        // Ensure the appointment belongs to the authenticated customer
        if ($appointment->customer_id !== $customer->custom_id) {
            abort(403, 'Unauthorized access to appointment.');
        }

        if (!$appointment->canBeCancelled()) {
            return redirect()->back()
                ->with('error', 'This appointment cannot be cancelled. Please contact us for assistance.');
        }

        $appointment->update([
            'status' => 'cancelled',
            'handled_at' => now(),
        ]);

        // Create notification for staff
        // Send real-time notification to staff about cancellation
        NotificationService::appointmentCancelled($appointment);

        // Send cancellation email to customer
        try {
            Mail::to($customer->email)->queue(new AppointmentCancelledMail($appointment));
        } catch (\Exception $e) {
            \Log::error('Failed to send appointment cancelled email', [
                'error' => $e->getMessage(),
                'appointment_id' => $appointment->id,
                'customer_email' => $customer->email,
            ]);
        }

        return redirect()->route('customer.appointments.index')
            ->with('success', 'Appointment cancelled successfully.');
    }
}
