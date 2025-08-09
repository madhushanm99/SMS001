<?php

namespace App\Jobs;

use App\Models\ServiceInvoice;
use App\Models\VehicleServiceSchedule;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class CalculateNextServiceSchedule implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $invoiceId;

    public function __construct(int $invoiceId)
    {
        $this->invoiceId = $invoiceId;
    }

    public function handle(): void
    {
        $invoice = ServiceInvoice::query()
            ->with(['vehicle'])
            ->find($this->invoiceId);

        if (!$invoice || !$invoice->vehicle_no) {
            return;
        }

        if (is_null($invoice->mileage) || !in_array($invoice->service_type, ['NS', 'FS'])) {
            return;
        }

        /** @var Collection<ServiceInvoice> $history */
        $history = ServiceInvoice::query()
            ->where('vehicle_no', $invoice->vehicle_no)
            ->whereIn('service_type', ['NS', 'FS'])
            ->whereNotNull('mileage')
            ->orderBy('invoice_date', 'asc')
            ->get(['id', 'invoice_date', 'mileage', 'service_type']);

        if ($history->isEmpty()) {
            return;
        }

        $rates = [];
        for ($i = 1; $i < $history->count(); $i++) {
            $prev = $history[$i - 1];
            $curr = $history[$i];

            if (!$prev->invoice_date || !$curr->invoice_date) {
                continue;
            }
            $days = Carbon::parse($prev->invoice_date)->diffInDays(Carbon::parse($curr->invoice_date)) ?: 1;
            $km = max(0, $curr->mileage - $prev->mileage);

            if ($km > 0 && $days > 0) {
                $rates[] = $km / $days;
            }
        }

        if (empty($rates) && $history->count() >= 2) {
            $prev = $history[$history->count() - 2];
            $curr = $history[$history->count() - 1];
            $days = Carbon::parse($prev->invoice_date)->diffInDays(Carbon::parse($curr->invoice_date)) ?: 1;
            $km = max(0, $curr->mileage - $prev->mileage);
            if ($km > 0 && $days > 0) {
                $rates[] = $km / $days;
            }
        }

        $kmPerDay = null;
        if (!empty($rates)) {
            sort($rates);
            $mid = intdiv(count($rates), 2);
            $kmPerDay = (count($rates) % 2 === 0)
                ? ($rates[$mid - 1] + $rates[$mid]) / 2
                : $rates[$mid];
        }

        $DAYS_CAP = 60;
        $TARGET_KM = 2500;

        if (empty($kmPerDay) || $kmPerDay <= 0) {
            $daysToTarget = $DAYS_CAP;
        } else {
            $daysToTarget = (int) ceil($TARGET_KM / $kmPerDay);
            if ($daysToTarget > $DAYS_CAP) {
                $daysToTarget = $DAYS_CAP;
            }
        }

        $nextServiceDate = Carbon::parse($invoice->invoice_date)->addDays($daysToTarget)->toDateString();
        $nextServiceMileage = (int) ($invoice->mileage + $TARGET_KM);

        VehicleServiceSchedule::updateOrCreate(
            ['vehicle_no' => $invoice->vehicle_no],
            [
                'last_service_invoice_id' => $invoice->id,
                'last_service_date' => $invoice->invoice_date,
                'last_mileage' => $invoice->mileage,
                'last_service_type' => $invoice->service_type,
                'next_service_date' => $nextServiceDate,
                'next_service_mileage' => $nextServiceMileage,
                'days_until_next' => $daysToTarget,
                'calculated_at' => now(),
            ]
        );
    }
}


