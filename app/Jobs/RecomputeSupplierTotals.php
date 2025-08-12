<?php

namespace App\Jobs;

use App\Models\Supplier;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RecomputeSupplierTotals implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $supplierCustomId;

    public function __construct(string $supplierCustomId)
    {
        $this->supplierCustomId = $supplierCustomId;
    }

    public function handle(): void
    {
        $supplier = Supplier::where('Supp_CustomID', $this->supplierCustomId)->first();
        if (!$supplier) {
            return;
        }

        // Update aggregates safely in background
        $supplier->refresh();
        $supplier->updateTotalSpent();
    }
}


