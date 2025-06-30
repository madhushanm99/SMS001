<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseReturn extends Model
{
    protected $fillable = [
        'return_no',
        'grn_id',
        'grn_no',
        'supp_Cus_ID',
        'note',
        'returned_by',
        'status'
    ];
    
    public function items()
    {
        return $this->hasMany(PurchaseReturnItem::class, 'purchase_return_id');
    }

    public function paymentTransactions()
    {
        return $this->hasMany(PaymentTransaction::class, 'purchase_return_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supp_Cus_ID', 'Supp_CustomID');
    }

    public function getTotalAmount()
    {
        return $this->items()->sum(\DB::raw('qty_returned * price'));
    }

    public function getTotalPayments()
    {
        return $this->paymentTransactions()
            ->where('status', 'completed')
            ->sum('amount');
    }

    public function getOutstandingAmount()
    {
        return $this->getTotalAmount() - $this->getTotalPayments();
    }

    public function isFullyPaid()
    {
        return $this->getOutstandingAmount() <= 0;
    }

    public function getPaymentStatus()
    {
        $outstanding = $this->getOutstandingAmount();
        $totalPayments = $this->getTotalPayments();

        if ($totalPayments == 0) {
            return 'unpaid';
        } elseif ($outstanding > 0) {
            return 'partially_paid';
        } else {
            return 'paid';
        }
    }

    public static function generateReturnNo(): string
    {
        $last = self::latest()->first();
        $number = $last ? ((int) substr($last->return_no, 2)) + 1 : 1;
        return 'PR' . str_pad($number, 6, '0', STR_PAD_LEFT);
    }
}
