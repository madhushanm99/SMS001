<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GRN extends Model
{
    protected $table = 'grn';
    protected $primaryKey = 'grn_id';

    protected $fillable = [
        'grn_no',
        'grn_date',
        'po_Auto_ID',
        'po_No',
        'supp_Cus_ID',
        'invoice_no',
        'invoice_date',
        'received_by',
        'note',
        'status',
    ];

    public function items()
    {
        return $this->hasMany(GRNItem::class, 'grn_id', 'grn_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supp_Cus_ID', 'Supp_CustomID');
    }

    public function paymentTransactions()
    {
        return $this->hasMany(PaymentTransaction::class, 'grn_id', 'grn_id');
    }

    public function getTotalPayments(): float
    {
        return $this->paymentTransactions()
            ->where('type', 'cash_out')
            ->where('status', 'completed')
            ->sum('amount');
    }

    public function getOutstandingAmount(): float
    {
        $total = $this->items->sum('line_total');
        return $total - $this->getTotalPayments();
    }

    public function isFullyPaid(): bool
    {
        return $this->getOutstandingAmount() <= 0;
    }

    public function getPaymentStatus(): string
    {
        $totalPayments = $this->getTotalPayments();
        $outstanding = $this->getOutstandingAmount();

        if ($totalPayments <= 0) {
            return 'unpaid';
        } elseif ($outstanding <= 0) {
            return 'paid';
        } else {
            return 'partially_paid';
        }
    }

    public function getTotalReturnedQuantity($itemId): int
    {
        return \App\Models\PurchaseReturnItem::whereHas('return', function($query) {
            $query->where('grn_id', $this->grn_id)->where('status', true);
        })
        ->where('item_ID', $itemId)
        ->sum('qty_returned');
    }

    public function getAvailableReturnQuantity($itemId): int
    {
        $grnItem = $this->items()->where('item_ID', $itemId)->first();
        if (!$grnItem) {
            return 0;
        }
        
        $totalReturned = $this->getTotalReturnedQuantity($itemId);
        return max(0, $grnItem->qty_received - $totalReturned);
    }

    public static function generateGRNNumber(): string
    {
        $lastGRN = self::latest('grn_no')->first();
        $number = $lastGRN ? (int) substr($lastGRN->grn_no, 3) + 1 : 1;
        return 'GRN' . str_pad($number, 6, '0', STR_PAD_LEFT);
    }

}

