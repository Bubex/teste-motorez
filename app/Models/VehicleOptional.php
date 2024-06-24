<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleOptional extends Model
{
    use HasFactory;

    protected $fillable = ['optional'];

    public function vehicles()
    {
        return $this->belongsToMany(Vehicle::class, 'vehicle_vehicle_optional');
    }
}
