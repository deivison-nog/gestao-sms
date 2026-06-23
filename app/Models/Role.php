<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug'];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function menuPermissions(): BelongsToMany
    {
        return $this->belongsToMany(MenuPermission::class, 'role_menu_permission');
    }

    public function syncMenuPermissions(array $keys): void
    {
        $permissionIds = MenuPermission::query()->whereIn('key', $keys)->pluck('id')->all();
        $this->menuPermissions()->sync($permissionIds);
    }
}
