<?php

namespace App\Http\Controllers;

use App\Mail\ServiceReminderMail;
use App\Models\Customer;
use App\Models\ServiceReminderLog;
use App\Models\Vehicle;
use App\Models\VehicleServiceSchedule;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ServiceScheduleController extends Controller
{
    /**
     * Display a listing of service schedules with basic filters.
     */
    public function index(Request $request)
    {
        $query = VehicleServiceSchedule::query()
            ->leftJoin('vehicles', 'vehicles.vehicle_no', '=', 'vehicle_service_schedules.vehicle_no')
            ->leftJoin('customers', 'customers.id', '=', 'vehicles.customer_id')
            ->select([
                'vehicle_service_schedules.*',
                'vehicles.customer_id',
                'vehicles.model as vehicle_model',
                'customers.name as customer_name',
                'customers.email as customer_email',
            ])
            ->latest('vehicle_service_schedules.updated_at');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('vehicle_service_schedules.vehicle_no', 'like', "%$search%")
                  ->orWhere('customers.name', 'like', "%$search%")
                  ->orWhere('customers.custom_id', 'like', "%$search%");
            });
        }

        if ($due = $request->input('due')) {
            // due options: upcoming, overdue, none
            if ($due === 'upcoming') {
                $query->whereNotNull('vehicle_service_schedules.next_service_date')
                      ->whereDate('vehicle_service_schedules.next_service_date', '>=', now()->toDateString());
            } elseif ($due === 'overdue') {
                $query->whereNotNull('vehicle_service_schedules.next_service_date')
                      ->whereDate('vehicle_service_schedules.next_service_date', '<', now()->toDateString());
            } elseif ($due === 'none') {
                $query->whereNull('vehicle_service_schedules.next_service_date');
            }
        }

        // Add aggregated attempt counts and last send info
        $query->addSelect([
            'total_attempts' => function ($sub) {
                $sub->from('service_reminder_logs as srl')
                    ->selectRaw('COUNT(*)')
                    ->whereColumn('srl.vehicle_no', 'vehicle_service_schedules.vehicle_no');
            },
            'manual_attempts' => function ($sub) {
                $sub->from('service_reminder_logs as srl2')
                    ->selectRaw('COUNT(*)')
                    ->whereColumn('srl2.vehicle_no', 'vehicle_service_schedules.vehicle_no')
                    ->where('srl2.source', 'manual');
            },
            'auto_attempts' => function ($sub) {
                $sub->from('service_reminder_logs as srl3')
                    ->selectRaw('COUNT(*)')
                    ->whereColumn('srl3.vehicle_no', 'vehicle_service_schedules.vehicle_no')
                    ->where('srl3.source', 'auto');
            },
            'last_sent_at' => function ($sub) {
                $sub->from('service_reminder_logs as srl4')
                    ->select('srl4.email_sent_at')
                    ->whereColumn('srl4.vehicle_no', 'vehicle_service_schedules.vehicle_no')
                    ->where('srl4.status', 'sent')
                    ->orderByDesc('srl4.email_sent_at')
                    ->limit(1);
            },
            'last_source' => function ($sub) {
                $sub->from('service_reminder_logs as srl5')
                    ->select('srl5.source')
                    ->whereColumn('srl5.vehicle_no', 'vehicle_service_schedules.vehicle_no')
                    ->orderByDesc('srl5.created_at')
                    ->limit(1);
            },
        ]);

        /** @var LengthAwarePaginator $schedules */
        $schedules = $query->paginate(10)->withQueryString();

        return view('service_schedules.index', compact('schedules'));
    }

    /**
     * Manually queue a service reminder email for a given vehicle number.
     */
    public function sendReminder(Request $request, string $vehicleNo): RedirectResponse
    {
        $vehicleNo = strtoupper($vehicleNo);

        $schedule = VehicleServiceSchedule::where('vehicle_no', $vehicleNo)->first();
        if (!$schedule) {
            return back()->with('error', 'Service schedule not found for vehicle ' . $vehicleNo);
        }

        $vehicle = Vehicle::where('vehicle_no', $vehicleNo)->first();
        if (!$vehicle) {
            return back()->with('error', 'Vehicle not found for ' . $vehicleNo);
        }

        /** @var Customer|null $customer */
        $customer = Customer::find($vehicle->customer_id);
        if (!$customer) {
            return back()->with('error', 'Customer not found for vehicle ' . $vehicleNo);
        }

        $email = $customer->email ?: ($customer->login->email ?? null);
        if (!$email) {
            return back()->with('error', 'No email found for customer ' . ($customer->name ?? ''));
        }

        $referenceDate = $schedule->next_service_date ? Carbon::parse($schedule->next_service_date) : now();
        $weekStart = $referenceDate->copy()->startOfWeek(Carbon::MONDAY);
        $weekEnd = $weekStart->copy()->endOfWeek(Carbon::SUNDAY);

        // Determine attempt number for the given week
        $attempt = (int) ServiceReminderLog::where('vehicle_no', $vehicleNo)
            ->whereDate('week_start', $weekStart->toDateString())
            ->max('attempt');
        $attempt = $attempt > 0 ? $attempt + 1 : 1;

        try {
            Mail::to($email)->queue(new ServiceReminderMail(
                $customer->name,
                $vehicleNo,
                optional($schedule->next_service_date)->format('Y-m-d')
            ));

            ServiceReminderLog::create([
                'vehicle_no' => $vehicleNo,
                'customer_custom_id' => $customer->custom_id ?? '',
                'customer_email' => $email,
                'next_service_date' => $schedule->next_service_date,
                'week_start' => $weekStart->toDateString(),
                'week_end' => $weekEnd->toDateString(),
                'attempt' => $attempt,
                'source' => 'manual',
                'status' => 'sent',
                'email_sent_at' => now(),
            ]);

            return back()->with('success', 'Reminder queued for ' . $vehicleNo . ' (attempt ' . $attempt . ').');
        } catch (\Throwable $e) {
            Log::error('Manual service reminder failed', [
                'vehicle_no' => $vehicleNo,
                'error' => $e->getMessage(),
            ]);

            ServiceReminderLog::create([
                'vehicle_no' => $vehicleNo,
                'customer_custom_id' => $customer->custom_id ?? '',
                'customer_email' => $email,
                'next_service_date' => $schedule->next_service_date,
                'week_start' => $weekStart->toDateString(),
                'week_end' => $weekEnd->toDateString(),
                'attempt' => $attempt,
                'source' => 'manual',
                'status' => 'error',
                'error_message' => $e->getMessage(),
            ]);

            return back()->with('error', 'Failed to queue reminder: ' . $e->getMessage());
        }
    }
}


