<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VehicleBrand extends Model
{
    protected $fillable = ['name', 'status'];
    public function vehicles()
    {
        return $this->hasMany(Vehicle::class, 'brand_id');
    }
}
