<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    protected $fillable = ['transaction_code', 'customer_id', 'customer_name_snapshot', 'delivery_run_id', 'created_by', 'qty', 'price', 'status', 'created_at'];

    protected function casts(): array
    {
        return ['created_at' => 'datetime', 'updated_at' => 'datetime'];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function deliveryRun(): BelongsTo
    {
        return $this->belongsTo(DeliveryRun::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function total(): int
    {
        return (int) $this->qty * (int) $this->price;
    }
}
