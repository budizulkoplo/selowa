<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DeliveryRun extends Model
{
    protected $fillable = ['delivery_vehicle_id', 'delivery_route_id', 'run_date', 'driver_name', 'area', 'notes', 'status'];

    protected function casts(): array
    {
        return ['run_date' => 'date'];
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(DeliveryVehicle::class, 'delivery_vehicle_id');
    }

    public function route(): BelongsTo
    {
        return $this->belongsTo(DeliveryRoute::class, 'delivery_route_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function label(): string
    {
        return $this->run_date?->format('d/m/Y').' - '.$this->vehicle?->name.' - '.($this->driver_name ?: $this->vehicle?->driverLabel() ?: '-').' - '.($this->area ?: $this->route?->name ?: '-');
    }
}
