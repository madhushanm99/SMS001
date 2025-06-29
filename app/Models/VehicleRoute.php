<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VehicleRoute extends Model
{
    protected $fillable = ['name', 'status'];

    public function vehicles()
{
    return $this->hasMany(Vehicle::class, 'route_id');
}
}
