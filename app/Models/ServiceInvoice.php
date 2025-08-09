<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceInvoice extends Model
{
    protected $fillable = [
        'invoice_no',
        'customer_id',
        'vehicle_no',
        'mileage',
        'service_type',
        'invoice_date',
        'job_total',
        'parts_total',
        'grand_total',
        'notes',
        'status',
        'created_by',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'job_total' => 'decimal:2',
        'parts_total' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'mileage' => 'integer',
    ];

    // Relationships
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'custom_id');
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_no', 'vehicle_no');
    }

    public function items(): HasMany
    {
        return $this->hasMany(ServiceInvoiceItem::class, 'service_invoice_id');
    }

    public function jobItems(): HasMany
    {
        return $this->hasMany(ServiceInvoiceItem::class, 'service_invoice_id')->where('item_type', 'job');
    }

    public function spareItems(): HasMany
    {
        return $this->hasMany(ServiceInvoiceItem::class, 'service_invoice_id')->where('item_type', 'spare');
    }

    public function paymentTransactions(): HasMany
    {
        return $this->hasMany(PaymentTransaction::class, 'service_invoice_id', 'id');
    }

    // Generate invoice number
    public static function generateInvoiceNo(): string
    {
        $lastInvoice = self::latest('id')->first();
        $number = $lastInvoice ? (int) substr($lastInvoice->invoice_no, 3) + 1 : 1;
        return 'SRV' . str_pad($number, 6, '0', STR_PAD_LEFT);
    }

    // Calculate totals
    public function calculateTotals(): void
    {
        $this->job_total = $this->jobItems()->sum('line_total');
        $this->parts_total = $this->spareItems()->sum('line_total');
        $this->grand_total = $this->job_total + $this->parts_total;
        $this->save();
    }

    // Payment-related methods
    public function getTotalPayments(): float
    {
        return $this->paymentTransactions()
            ->where('type', 'cash_in')
            ->where('status', 'completed')
            ->sum('amount');
    }

    public function getOutstandingAmount(): float
    {
        return $this->grand_total - $this->getTotalPayments();
    }

    public function isFullyPaid(): bool
    {
        return $this->getOutstandingAmount() <= 0;
    }

    public function isPartiallyPaid(): bool
    {
        $totalPayments = $this->getTotalPayments();
        return $totalPayments > 0 && $totalPayments < $this->grand_total;
    }

    public function isUnpaid(): bool
    {
        return $this->getTotalPayments() == 0;
    }

    public function getPaymentStatus(): string
    {
        if ($this->isFullyPaid()) {
            return 'fully_paid';
        } elseif ($this->isPartiallyPaid()) {
            return 'partially_paid';
        } else {
            return 'unpaid';
        }
    }

    public function canBeFinalized(): bool
    {
        return $this->status === 'hold' && $this->items()->count() > 0;
    }

    public function finalize(): bool
    {
        if (!$this->canBeFinalized()) {
            return false;
        }

        $this->status = 'finalized';
        $this->calculateTotals();

        return true;
    }

    // Service type methods
    public function getServiceTypeLabel(): string
    {
        return match($this->service_type) {
            'NS' => 'Normal Service',
            'FS' => 'Full Service',
            default => 'Unknown Service'
        };
    }

    public function getServiceTypeColor(): string
    {
        return match($this->service_type) {
            'NS' => 'primary',
            'FS' => 'success',
            default => 'secondary'
        };
    }

    public function isFullService(): bool
    {
        return $this->service_type === 'FS';
    }

    public function isNormalService(): bool
    {
        return $this->service_type === 'NS';
    }

        // Auto-determine service type based on job types
    public function determineServiceType(): void
    {
        $jobItems = $this->jobItems()->with('jobType')->get();

        if ($jobItems->isEmpty()) {
            $this->service_type = null;
            $this->save();
            return;
        }

        $hasFullServiceJob = false;
        $hasNormalServiceJob = false;
        $jobTypeNames = [];

        foreach ($jobItems as $item) {
            $jobType = $item->jobType?->jobType ?? $item->item_name;
            $jobTypeNames[] = $jobType;

            if ($this->isFullServiceJobType($jobType)) {
                $hasFullServiceJob = true;
            }

            if ($this->isNormalServiceJobType($jobType)) {
                $hasNormalServiceJob = true;
            }
        }

        // Determine service type based on job analysis
        $oldServiceType = $this->service_type;

        if ($hasFullServiceJob) {
            $this->service_type = 'FS';
        } elseif ($hasNormalServiceJob) {
            $this->service_type = 'NS';
        } else {
            // Default to NS if no specific keywords found
            $this->service_type = 'NS';
        }

        // Log service type determination for debugging
        \Log::info('Service type determined', [
            'invoice_id' => $this->id,
            'invoice_no' => $this->invoice_no,
            'job_types' => $jobTypeNames,
            'has_full_service' => $hasFullServiceJob,
            'has_normal_service' => $hasNormalServiceJob,
            'old_service_type' => $oldServiceType,
            'new_service_type' => $this->service_type,
        ]);

        $this->save();
    }

    // Manual service type setter
    public function setServiceType(string $serviceType): void
    {
        if (in_array($serviceType, ['NS', 'FS'])) {
            $this->service_type = $serviceType;
            $this->save();
        }
    }

    // Helper method to check for normal service job types
    private function isNormalServiceJobType(string $jobType): bool
    {
        $normalServiceKeywords = [
            'normal service',
            'basic service',
            'minor service',
            'routine service',
            'regular service',
            'standard service',
            'oil change',
            'oil service',
            'filter change',
            'basic maintenance',
            'routine maintenance',
            'minor repair',
            'inspection',
            'check up',
            'tune up',
            'ns',
            'normal',
            'basic',
            'minor',
            'routine'
        ];

        $jobTypeLower = strtolower(trim($jobType));

        foreach ($normalServiceKeywords as $keyword) {
            if (str_contains($jobTypeLower, $keyword)) {
                return true;
            }
        }

        return false;
    }

    // Helper method to determine if a job type indicates full service
    private function isFullServiceJobType(string $jobType): bool
    {
        $fullServiceKeywords = [
            // Full Service Keywords
            'full service',
            'complete service',
            'major service',
            'comprehensive service',
            'full maintenance',
            'complete maintenance',
            'annual service',
            'yearly service',

            // Major Component Services
            'engine overhaul',
            'engine service',
            'transmission service',
            'transmission overhaul',
            'brake service',
            'brake overhaul',
            'suspension service',
            'suspension overhaul',
            'clutch service',
            'clutch replacement',
            'gearbox service',
            'differential service',

            // Comprehensive Maintenance
            'timing belt',
            'timing chain',
            'cylinder head',
            'engine rebuild',
            'major repair',
            'overhaul',
            'restoration',

            // Full Service Indicators
            'fs',
            'full',
            'complete',
            'comprehensive',
            'major'
        ];

        $jobTypeLower = strtolower(trim($jobType));

        // Check for full service keywords
        foreach ($fullServiceKeywords as $keyword) {
            if (str_contains($jobTypeLower, $keyword)) {
                return true; // Full service
            }
        }

        return false;
    }
}
