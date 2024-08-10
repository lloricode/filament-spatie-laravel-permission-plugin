<?php

declare(strict_types=1);

namespace Lloricode\FilamentSpatieLaravelPermissionPlugin\Models;

use Illuminate\Database\Eloquent\HasBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Models\EloquentBuilder\RoleEloquentBuilder;

/**
 * @property string $uuid
 * @property string $name
 * @property string $guard_name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Model> $users
 * @property-read int|null $users_count
 *
 * @method static \Lloricode\FilamentSpatieLaravelPermissionPlugin\Models\EloquentBuilder\RoleEloquentBuilder|Role newModelQuery()
 * @method static \Lloricode\FilamentSpatieLaravelPermissionPlugin\Models\EloquentBuilder\RoleEloquentBuilder|Role newQuery()
 * @method static \Lloricode\FilamentSpatieLaravelPermissionPlugin\Models\EloquentBuilder\RoleEloquentBuilder|Role permission($permissions, $without = false)
 * @method static \Lloricode\FilamentSpatieLaravelPermissionPlugin\Models\EloquentBuilder\RoleEloquentBuilder|Role query()
 * @method static \Lloricode\FilamentSpatieLaravelPermissionPlugin\Models\EloquentBuilder\RoleEloquentBuilder|Role whereNotSuperAdmin()
 * @method static \Lloricode\FilamentSpatieLaravelPermissionPlugin\Models\EloquentBuilder\RoleEloquentBuilder|Role withoutPermission($permissions)
 */
class Role extends \Spatie\Permission\Models\Role
{
    /** @use HasBuilder<RoleEloquentBuilder> */
    use HasBuilder;

    protected static string $builder = RoleEloquentBuilder::class;

    public static function superAdmin(): self
    {
        return Role::findByNameOnceCached(config('filament-permission.roles.super_admin'));
    }

    public static function admin(): self
    {
        return Role::findByNameOnceCached(config('filament-permission.roles.admin'));
    }

    public static function findByNameOnceCached(string $name, ?string $guard = null): self
    {
        return once(function () use ($name, $guard) {
            /** @var self $role */
            $role = static::findByName($name, $guard ?? Config::string('filament-permission.guard'));

            return $role;
        });
    }
}
