<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    protected $fillable = ['village_id', 'registered_by', 'registered_at', 'name', 'phone', 'rw', 'rt', 'timetable', 'customer_price', 'is_member', 'discount_type', 'discount_value', 'is_active'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_member' => 'boolean',
            'registered_at' => 'datetime',
        ];
    }

    public function village(): BelongsTo
    {
        return $this->belongsTo(Village::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function registeredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registered_by');
    }

    public function addressLabel(): string
    {
        $village = $this->village;

        if (! $village) {
            return '-';
        }

        return collect([
            'RT '.$this->rt,
            'RW '.$this->rw,
            $village->name,
            $village->district?->name,
            $village->district?->city?->name,
        ])->filter(fn ($value) => filled(str_replace(['RT ', 'RW '], '', (string) $value)))->join(', ');
    }

    public function effectivePrice(): int
    {
        $price = (int) $this->customer_price;

        if (! $this->is_member || $this->discount_type === 'none' || $this->discount_value < 1) {
            return $price;
        }

        if ($this->discount_type === 'percent') {
            return max(0, $price - (int) round($price * $this->discount_value / 100));
        }

        return max(0, $price - (int) $this->discount_value);
    }
}
