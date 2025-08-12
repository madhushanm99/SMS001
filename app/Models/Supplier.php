<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

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

    public function grns(): HasMany
    {
        return $this->hasMany(GRN::class, 'supp_Cus_ID', 'Supp_CustomID');
    }

    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(Po::class, 'supp_Cus_ID', 'Supp_CustomID');
    }

    /**
     * Purchase returns made to this supplier
     */
    public function purchaseReturns(): HasMany
    {
        return $this->hasMany(PurchaseReturn::class, 'supp_Cus_ID', 'Supp_CustomID');
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

    /**
     * Get outstanding supplier credit from purchase returns that have not yet been refunded
     *
     * Returns a positive value representing how much the supplier owes us (credit to apply).
     */
    public function getOutstandingPurchaseReturnCredit(): float
    {
        // Total value of returns for this supplier (sum of qty_returned * price)
        $totalReturnValue = \DB::table('purchase_return_items')
            ->join('purchase_returns', 'purchase_return_items.purchase_return_id', '=', 'purchase_returns.id')
            ->where('purchase_returns.supp_Cus_ID', $this->Supp_CustomID)
            ->where('purchase_returns.status', true)
            ->selectRaw('COALESCE(SUM(purchase_return_items.qty_returned * purchase_return_items.price), 0) as total')
            ->value('total') ?? 0.0;

        // Total refunds already received (payments recorded against purchase returns)
        $totalRefunds = \DB::table('payment_transactions')
            ->where('supplier_id', $this->Supp_CustomID)
            ->whereNotNull('purchase_return_id')
            ->where('status', 'completed')
            ->selectRaw('COALESCE(SUM(amount), 0) as total')
            ->value('total') ?? 0.0;

        $outstanding = (float) $totalReturnValue - (float) $totalRefunds;
        return $outstanding > 0 ? $outstanding : 0.0;
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
