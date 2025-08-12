<?php

namespace App\Jobs;

use App\Mail\ServiceReminderMail;
use App\Models\Customer;
use App\Models\ServiceInvoice;
use App\Models\ServiceReminderLog;
use App\Models\Vehicle;
use App\Models\VehicleServiceSchedule;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendServiceReminders implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
    }

    public function handle(): void
    {
        $now = Carbon::now();
        // Determine upcoming week range (Mon-Sun) starting tomorrow or from next Monday
        $weekStart = $now->copy()->startOfWeek(Carbon::MONDAY)->addWeek();
        $weekEnd = $weekStart->copy()->endOfWeek(Carbon::SUNDAY);

        // But since we run on Sunday 19:00, the upcoming week is next Mon..Sun
        // Select schedules where next_service_date falls within [weekStart, weekEnd]
        $schedules = VehicleServiceSchedule::query()
            ->whereNotNull('next_service_date')
            ->whereBetween('next_service_date', [$weekStart->toDateString(), $weekEnd->toDateString()])
            ->get();

        foreach ($schedules as $schedule) {
            // Find vehicle and customer
            $vehicle = Vehicle::where('vehicle_no', $schedule->vehicle_no)->first();
            if (!$vehicle) {
                $this->logSkip($schedule, $weekStart, $weekEnd, 'skipped', 'Vehicle not found');
                continue;
            }

            $customer = Customer::find($vehicle->customer_id);
            if (!$customer) {
                $this->logSkip($schedule, $weekStart, $weekEnd, 'skipped', 'Customer not found');
                continue;
            }

            $email = $customer->email ?: $customer->login->email ?? null;
            if (!$email) {
                $this->logSkip($schedule, $weekStart, $weekEnd, 'no_email', 'No customer email');
                continue;
            }

            // Determine attempt number: If already sent for this vehicle + week, skip initial and prepare follow-up next week
            $attempt = 1;
            $existing = ServiceReminderLog::where('vehicle_no', $schedule->vehicle_no)
                ->where('week_start', $weekStart->toDateString())
                ->first();
            if ($existing) {
                // Already processed this week
                continue;
            }

            // Check if service already completed within this coming week; if yes, mark fulfilled and skip sending
            $alreadyPlanned = ServiceInvoice::query()
                ->where('vehicle_no', $schedule->vehicle_no)
                ->whereBetween('invoice_date', [$weekStart->toDateString(), $weekEnd->toDateString()])
                ->exists();
            if ($alreadyPlanned) {
                $this->logSkip($schedule, $weekStart, $weekEnd, 'fulfilled', 'Service already done or planned in week');
                continue;
            }

            try {
                Mail::to($email)->queue(new ServiceReminderMail(
                    $customer->name,
                    $schedule->vehicle_no,
                    optional($schedule->next_service_date)->format('Y-m-d')
                ));

                ServiceReminderLog::create([
                    'vehicle_no' => $schedule->vehicle_no,
                    'customer_custom_id' => $customer->custom_id ?? '',
                    'customer_email' => $email,
                    'next_service_date' => $schedule->next_service_date,
                    'week_start' => $weekStart->toDateString(),
                    'week_end' => $weekEnd->toDateString(),
                    'attempt' => $attempt,
                    'source' => 'auto',
                    'status' => 'sent',
                    'email_sent_at' => now(),
                ]);
            } catch (\Throwable $e) {
                Log::error('Service reminder email failed', [
                    'vehicle_no' => $schedule->vehicle_no,
                    'error' => $e->getMessage(),
                ]);
                ServiceReminderLog::create([
                    'vehicle_no' => $schedule->vehicle_no,
                    'customer_custom_id' => $customer->custom_id ?? '',
                    'customer_email' => $email,
                    'next_service_date' => $schedule->next_service_date,
                    'week_start' => $weekStart->toDateString(),
                    'week_end' => $weekEnd->toDateString(),
                    'attempt' => $attempt,
                    'source' => 'auto',
                    'status' => 'error',
                    'error_message' => $e->getMessage(),
                ]);
            }
        }

        // Follow-up for previous week: if not fulfilled and customer didn't come, send one more reminder
        $prevWeekStart = $weekStart->copy()->subWeek();
        $prevWeekEnd = $prevWeekStart->copy()->endOfWeek(Carbon::SUNDAY);

        $pendingFollowUps = ServiceReminderLog::query()
            ->where('week_start', $prevWeekStart->toDateString())
            ->where('status', 'sent')
            ->get();

        foreach ($pendingFollowUps as $log) {
            // Did customer come last week?
            $done = ServiceInvoice::query()
                ->where('vehicle_no', $log->vehicle_no)
                ->whereBetween('invoice_date', [$prevWeekStart->toDateString(), $prevWeekEnd->toDateString()])
                ->exists();
            if ($done) {
                // Mark previous as fulfilled and skip sending follow-up
                $log->update(['status' => 'fulfilled']);
                continue;
            }

            // Send follow-up for current upcoming week (attempt 2) only if not already sent for this new week
            $alreadyThisWeek = ServiceReminderLog::where('vehicle_no', $log->vehicle_no)
                ->where('week_start', $weekStart->toDateString())
                ->exists();
            if ($alreadyThisWeek) {
                continue;
            }

            $vehicle = Vehicle::where('vehicle_no', $log->vehicle_no)->first();
            if (!$vehicle) {
                continue;
            }
            $customer = Customer::find($vehicle->customer_id);
            if (!$customer) {
                continue;
            }
            $email = $customer->email ?: $customer->login->email ?? null;
            if (!$email) {
                continue;
            }

            try {
                Mail::to($email)->queue(new ServiceReminderMail(
                    $customer->name,
                    $log->vehicle_no,
                    $log->next_service_date?->format('Y-m-d')
                ));

                ServiceReminderLog::create([
                    'vehicle_no' => $log->vehicle_no,
                    'customer_custom_id' => $customer->custom_id ?? '',
                    'customer_email' => $email,
                    'next_service_date' => $log->next_service_date,
                    'week_start' => $weekStart->toDateString(),
                    'week_end' => $weekEnd->toDateString(),
                    'attempt' => 2,
                    'source' => 'auto',
                    'status' => 'sent',
                    'email_sent_at' => now(),
                ]);
            } catch (\Throwable $e) {
                Log::error('Follow-up service reminder email failed', [
                    'vehicle_no' => $log->vehicle_no,
                    'error' => $e->getMessage(),
                ]);
                ServiceReminderLog::create([
                    'vehicle_no' => $log->vehicle_no,
                    'customer_custom_id' => $customer->custom_id ?? '',
                    'customer_email' => $email,
                    'next_service_date' => $log->next_service_date,
                    'week_start' => $weekStart->toDateString(),
                    'week_end' => $weekEnd->toDateString(),
                    'attempt' => 2,
                    'source' => 'auto',
                    'status' => 'error',
                    'error_message' => $e->getMessage(),
                ]);
            }
        }
    }

    private function logSkip(VehicleServiceSchedule $schedule, Carbon $weekStart, Carbon $weekEnd, string $status, string $reason): void
    {
        ServiceReminderLog::create([
            'vehicle_no' => $schedule->vehicle_no,
            'customer_custom_id' => '',
            'customer_email' => null,
            'next_service_date' => $schedule->next_service_date,
            'week_start' => $weekStart->toDateString(),
            'week_end' => $weekEnd->toDateString(),
            'attempt' => 1,
            'source' => 'auto',
            'status' => $status,
            'error_message' => $reason,
        ]);
    }
}


