<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    protected $fillable = [
        'customer_id',
        'vehicle_no',
        'brand_id',
        'model',
        'engine_no',
        'chassis_no',
        'route_id',
        'year_of_manufacture',
        'date_of_purchase',
        'last_entry',
        'registration_status',
        'is_approved',
        'approved_by',
        'approved_at',
        'status',
    ];
    protected $casts = [
        'date_of_purchase' => 'date',
        'last_entry' => 'datetime',
        'year_of_manufacture' => 'integer',
        'status' => 'boolean',
        'registration_status' => 'boolean',
        'is_approved' => 'boolean',
        'approved_at' => 'datetime',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function brand()
    {
        return $this->belongsTo(VehicleBrand::class);
    }

    public function route()
    {
        return $this->belongsTo(VehicleRoute::class);
    }

    public function serviceSchedule()
    {
        return $this->hasOne(VehicleServiceSchedule::class, 'vehicle_no', 'vehicle_no');
    }
}
