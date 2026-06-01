<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DeliveryRoute extends Model
{
    protected $fillable = ['delivery_vehicle_id', 'day_of_week', 'name', 'area', 'notes', 'is_active'];

    protected function casts(): array
    {
        return [
            'day_of_week' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(DeliveryVehicle::class, 'delivery_vehicle_id');
    }

    public function runs(): HasMany
    {
        return $this->hasMany(DeliveryRun::class);
    }

    public function dayName(): string
    {
        return ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'][$this->day_of_week] ?? '-';
    }

    public function label(): string
    {
        return $this->dayName().' - '.$this->vehicle?->name.' - '.$this->name;
    }
}
