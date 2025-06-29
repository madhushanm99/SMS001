<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierGroup extends Model
{
    protected $fillable = [
        'name',
        'description',
    ]; // Allow mass assignment

    public function suppliers()
    {
        return $this->hasMany(Supplier::class); // Assuming you want to relate Supplier with SupplierGroup
    }
}
