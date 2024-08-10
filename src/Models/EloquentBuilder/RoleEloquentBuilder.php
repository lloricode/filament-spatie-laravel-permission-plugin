<?php

declare(strict_types=1);

namespace Lloricode\FilamentSpatieLaravelPermissionPlugin\Models\EloquentBuilder;

use Illuminate\Database\Eloquent\Builder;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Models\Role;

/**
 * @extends \Illuminate\Database\Eloquent\Builder<Role>
 *
 * @mixin Role
 */
class RoleEloquentBuilder extends Builder
{
    public function whereNotSuperAdmin(): self
    {
        return $this
            ->whereNot('name', config('domain.access.role.super_admin'));
    }
}
