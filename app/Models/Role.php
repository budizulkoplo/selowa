<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    public function menus(): BelongsToMany
    {
        return $this->belongsToMany(Menu::class, 'menu_role')->withTimestamps();
    }
}
