<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    use HasFactory;

    protected $primaryKey = 'Supp_ID';

    public function getRouteKeyName()
    {
        return 'Supp_ID';
    }

    protected $fillable = [
        'Supp_ID',
        'Supp_CustomID',
        'Supp_Name',
        'Company_Name',
        'Phone',
        'Fax',
        'Email',
        'Web',
        'Address1',
        'Supp_Group_Name',
        'Remark',
        'Last_GRN',
        'Total_Orders',
        'Total_Spent',
    ];

    protected $casts = [
        'Total_Orders' => 'decimal:2',
        'Total_Spent' => 'decimal:2',
    ];

    // Relationships
    public function paymentTransactions(): HasMany
    {
        return $this->hasMany(PaymentTransaction::class, 'supplier_id', 'Supp_CustomID');
    }

    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(Po::class, 'supp_Cus_ID', 'Supp_CustomID');
    }

    // Payment-related methods
    public function getTotalPayments(): float
    {
        return $this->paymentTransactions()
            ->where('type', 'cash_out')
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

    public function getTotalOutstanding(): float
    {
        $totalOrders = $this->purchaseOrders()
            ->where('orderStatus', 'received')
            ->sum('grand_Total');
        
        return $totalOrders - $this->getTotalPayments();
    }

    public function updateTotalSpent(): void
    {
        $totalPayments = $this->getTotalPayments();
        $this->update(['Total_Spent' => $totalPayments]);
    }

    // Helper methods
    public function getDisplayName(): string
    {
        return $this->Supp_Name ?: $this->Company_Name;
    }

    public static function generateSupplierID(): string
    {
        $lastSupplier = self::latest('Supp_ID')->first();
        $next = $lastSupplier ? $lastSupplier->Supp_ID + 1 : 1;
        return 'SUPP' . str_pad($next, 6, '0', STR_PAD_LEFT);
    }
}
