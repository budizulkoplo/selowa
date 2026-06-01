<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DeliveryVehicle extends Model
{
    protected $fillable = ['name', 'plate_number', 'driver_name', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function runs(): HasMany
    {
        return $this->hasMany(DeliveryRun::class);
    }
}
