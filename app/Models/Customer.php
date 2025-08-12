<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Carbon\Carbon;


class Customer extends Model
{
    protected $table = 'customers';

    protected $fillable = [
    'custom_id',
    'name',
    'phone',
    'email',
    'nic',
    'group_name',
    'address',
    'balance_credit',
    'last_visit',
    'user_id',
    'status',
];

    protected $casts = [
        'balance_credit' => 'decimal:2',
        'last_visit' => 'date',
        'status' => 'boolean',
    ];

    // Relationships
    public function paymentTransactions(): HasMany
    {
        return $this->hasMany(PaymentTransaction::class, 'customer_custom_id', 'custom_id');
    }

    public function salesInvoices(): HasMany
    {
        return $this->hasMany(SalesInvoice::class, 'customer_id', 'custom_id');
    }

    // Payment-related methods
    public function getTotalPayments(): float
    {
        return $this->paymentTransactions()
            ->where('type', 'cash_in')
            ->where('status', 'completed')
            ->sum('amount');
    }

    public function getRecentPayments(int $limit = 5): \Illuminate\Database\Eloquent\Collection
    {
        return $this->paymentTransactions()
            ->latest('transaction_date')
            ->limit($limit)
            ->get();
    }

    public function updateCreditBalance(): void
    {
        $totalInvoices = $this->salesInvoices()->sum('grand_total');
        $totalPayments = $this->getTotalPayments();

        $this->update([
            'balance_credit' => $totalInvoices - $totalPayments
        ]);
    }

public static function generateCustomID(): string
{
    $last = self::latest('id')->first();
    $next = $last ? ((int)substr($last->custom_id, 4)) + 1 : 1;
    return 'CUST' . str_pad($next, 6, '0', STR_PAD_LEFT);
}

public function login()
{
    return $this->hasOne(CustomerLogin::class, 'customer_custom_id', 'custom_id');
}

public function vehicles(): HasMany
{
    return $this->hasMany(Vehicle::class, 'customer_id');
}

public function serviceInvoices(): HasMany
{
    return $this->hasMany(ServiceInvoice::class, 'customer_id', 'custom_id');
}

public function appointments(): HasMany
{
    return $this->hasMany(Appointment::class, 'customer_id', 'custom_id');
}

    /**
     * Update the customer's last visit date to the provided date (or today).
     * Only updates if the new date is later than the current value.
     */
    public function updateLastVisit(null|string|\DateTimeInterface $date = null): void
    {
        $newDate = $date ? Carbon::parse($date)->toDateString() : now()->toDateString();

        $currentDate = $this->last_visit ? Carbon::parse($this->last_visit)->toDateString() : null;

        if ($currentDate === null || $newDate > $currentDate) {
            $this->last_visit = $newDate;
            $this->save();
        }
    }
}
