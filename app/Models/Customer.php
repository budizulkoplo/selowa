<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    protected $fillable = ['village_id', 'name', 'phone', 'rw', 'rt', 'timetable', 'customer_price', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function village(): BelongsTo
    {
        return $this->belongsTo(Village::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
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
}
