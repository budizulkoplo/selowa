<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gallon extends Model
{
    protected $fillable = ['name', 'stock', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }
}
