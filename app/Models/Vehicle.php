<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'brand',
        'model',
        'year',
        'version',
        'color',
        'mileage',
        'fuel',
        'transmission',
        'doors',
        'price',
        'last_update'
    ];

    public function optionals()
    {
        return $this->belongsToMany(VehicleOptional::class, 'vehicle_vehicle_optional');
    }
}
