<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Notification;
use App\Mail\AppointmentConfirmedMail;
use App\Mail\AppointmentRejectedMail;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class StaffAppointmentController extends Controller
{
    /**
     * Display a listing of appointments for staff.
     */
    public function index(Request $request)
    {
        $query = Appointment::with(['customer', 'vehicle.brand']);

        // Filter by status if provided
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by service type if provided
        if ($request->filled('service_type')) {
            $query->where('service_type', $request->service_type);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->where('appointment_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('appointment_date', '<=', $request->date_to);
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('appointment_no', 'like', "%$search%")
                  ->orWhere('vehicle_no', 'like', "%$search%")
                  ->orWhereHas('customer', function ($c) use ($search) {
                      $c->where('name', 'like', "%$search%")
                        ->orWhere('phone', 'like', "%$search%");
                  });
            });
        }

        $appointments = $query->latest('appointment_date')->latest('appointment_time')->paginate(10)->withQueryString();

        // Calculate summary statistics
        $totalAppointments = Appointment::count();
        $pendingAppointments = Appointment::pending()->count();
        $confirmedAppointments = Appointment::confirmed()->count();
        $todayAppointments = Appointment::forDate(now()->toDateString())
            ->whereIn('status', ['confirmed', 'completed'])
            ->count();

        return view('appointments.index', compact(
            'appointments',
            'totalAppointments',
            'pendingAppointments',
            'confirmedAppointments',
            'todayAppointments'
        ));
    }

    /**
     * Display the specified appointment.
     */
    public function show(Appointment $appointment)
    {
        $appointment->load(['customer.serviceInvoices', 'vehicle.brand']);
        return view('appointments.show', compact('appointment'));
    }

    /**
     * Confirm the specified appointment.
     */
    public function confirm(Request $request, Appointment $appointment)
    {
        if (!$appointment->canBeConfirmed()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This appointment cannot be confirmed.'
                ], 422);
            }
            return redirect()->back()->with('error', 'This appointment cannot be confirmed.');
        }

        $request->validate([
            'staff_notes' => 'nullable|string|max:1000',
        ]);

        $appointment->update([
            'status' => 'confirmed',
            'staff_notes' => $request->staff_notes,
            'handled_by' => Auth::user()->name ?? Auth::user()->email,
            'handled_at' => now(),
        ]);

        // Confirmation notifications disabled per requirements

        // Send confirmation email to customer (queued)
        $this->sendAppointmentEmail($appointment, 'confirmed');

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Appointment confirmed successfully. Customer has been notified.'
            ]);
        }
        return redirect()->back()->with('success', 'Appointment confirmed successfully. Customer has been notified.');
    }

    /**
     * Reject the specified appointment.
     */
    public function reject(Request $request, Appointment $appointment)
    {
        if (!$appointment->canBeRejected()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This appointment cannot be rejected.'
                ], 422);
            }
            return redirect()->back()->with('error', 'This appointment cannot be rejected.');
        }

        $request->validate([
            'staff_notes' => 'required|string|max:1000',
        ]);

        $appointment->update([
            'status' => 'rejected',
            'staff_notes' => $request->staff_notes,
            'handled_by' => Auth::user()->name ?? Auth::user()->email,
            'handled_at' => now(),
        ]);

        // Send real-time notification about rejection
        NotificationService::appointmentRejected($appointment, Auth::user()->name ?? Auth::user()->email, $request->staff_notes);

        // Send rejection email to customer (queued)
        $this->sendAppointmentEmail($appointment, 'rejected');

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Appointment rejected successfully. Customer has been notified.'
            ]);
        }
        return redirect()->back()->with('success', 'Appointment rejected successfully. Customer has been notified.');
    }

    /**
     * Mark appointment as completed.
     */
    public function complete(Request $request, Appointment $appointment)
    {
        if ($appointment->status !== 'confirmed') {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only confirmed appointments can be marked as completed.'
                ], 422);
            }
            return redirect()->back()->with('error', 'Only confirmed appointments can be marked as completed.');
        }

        $request->validate([
            'staff_notes' => 'nullable|string|max:1000',
        ]);

        $appointment->update([
            'status' => 'completed',
            'staff_notes' => $request->staff_notes,
            'handled_by' => Auth::user()->name ?? Auth::user()->email,
            'handled_at' => now(),
        ]);

        // Update customer's last visit based on completed appointment date
        if ($appointment->customer) {
            $appointment->customer->updateLastVisit($appointment->appointment_date);
        }

        // Completion notifications disabled per requirements

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Appointment marked as completed successfully.'
            ]);
        }
        return redirect()->back()->with('success', 'Appointment marked as completed successfully.');
    }

    /**
     * Display calendar view of appointments.
     */
    public function calendar(Request $request)
    {
        $year = $request->get('year', now()->year);
        $month = $request->get('month', now()->month);

        $currentDate = \Carbon\Carbon::create($year, $month, 1);
        $currentMonth = $currentDate->format('F Y');

        // Get first day of calendar (might be from previous month)
        $startDate = $currentDate->copy()->startOfMonth()->startOfWeek();
        $endDate = $currentDate->copy()->endOfMonth()->endOfWeek();

        // Get all appointments for this period
        $appointments = Appointment::with(['customer', 'vehicle.brand'])
            ->whereBetween('appointment_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->get()
            ->groupBy('appointment_date');

        // Build calendar days
        $calendarDays = [];
        $currentDay = $startDate->copy();

        while ($currentDay <= $endDate) {
            $dayAppointments = $appointments->get($currentDay->format('Y-m-d'), collect());

            $calendarDays[] = [
                'date' => $currentDay->format('Y-m-d'),
                'day' => $currentDay->day,
                'isToday' => $currentDay->isToday(),
                'isOtherMonth' => $currentDay->month !== (int)$month,
                'appointments' => $dayAppointments
            ];

            $currentDay->addDay();
        }

        return view('appointments.calendar', compact('calendarDays', 'currentMonth', 'currentDate'));
    }

    /**
     * Get appointment calendar data for AJAX requests.
     */
    public function getCalendarData(Request $request)
    {
        $start = $request->get('start');
        $end = $request->get('end');

        $appointments = Appointment::with(['customer'])
            ->whereBetween('appointment_date', [$start, $end])
            ->whereIn('status', ['pending', 'confirmed', 'completed'])
            ->get()
            ->map(function ($appointment) {
                return [
                    'id' => $appointment->id,
                    'title' => $appointment->customer->name . ' - ' . $appointment->getServiceTypeLabel(),
                    'start' => $appointment->appointment_date->format('Y-m-d') . 'T' . $appointment->appointment_time,
                    'backgroundColor' => $this->getCalendarColor($appointment->status),
                    'borderColor' => $this->getCalendarColor($appointment->status),
                    'extendedProps' => [
                        'appointment_no' => $appointment->appointment_no,
                        'customer_name' => $appointment->customer->name,
                        'vehicle_no' => $appointment->vehicle_no,
                        'service_type' => $appointment->getServiceTypeLabel(),
                        'status' => $appointment->getStatusLabel(),
                    ],
                ];
            });

        return response()->json($appointments);
    }

        /**
     * Send appointment email to customer.
     */
    private function sendAppointmentEmail(Appointment $appointment, string $type)
    {
        try {
            $customer = $appointment->customer;

            switch ($type) {
                case 'confirmed':
                    Mail::to($customer->email)->queue(new AppointmentConfirmedMail($appointment));
                    break;
                case 'rejected':
                    Mail::to($customer->email)->queue(new AppointmentRejectedMail($appointment));
                    break;
            }

            \Log::info('Appointment email sent successfully', [
                'appointment_id' => $appointment->id,
                'customer_email' => $customer->email,
                'type' => $type,
                'appointment_no' => $appointment->appointment_no,
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to send appointment email', [
                'error' => $e->getMessage(),
                'appointment_id' => $appointment->id,
                'customer_email' => $appointment->customer->email ?? 'unknown',
                'type' => $type,
            ]);
        }
    }

    /**
     * Get calendar color based on appointment status.
     */
    private function getCalendarColor(string $status): string
    {
        return match($status) {
            'pending' => '#ffc107',
            'confirmed' => '#28a745',
            'completed' => '#17a2b8',
            'rejected' => '#dc3545',
            'cancelled' => '#6c757d',
            default => '#6c757d'
        };
    }
}
