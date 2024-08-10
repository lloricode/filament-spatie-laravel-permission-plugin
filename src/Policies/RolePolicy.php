<?php

declare(strict_types=1);

namespace Lloricode\FilamentSpatieLaravelPermissionPlugin\Policies;

use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Config;
use Spatie\Permission\Contracts\Role as RoleContract;

class RolePolicy
{
    use ChecksWildcardPermissions;

    public function before(?User $user, string $ability, mixed $role = null): ?bool
    {
        if ($role instanceof RoleContract) {

            if (in_array($role->name, Config::array('filament-permission.roles'), true)) {
                return false;
            }

            if (in_array($role->name, Config::array('filament-permission.extra_roles'), true)) {
                return false;
            }
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return $this->checkWildcardPermissions($user);
    }

    public function create(User $user): bool
    {
        return $this->checkWildcardPermissions($user);
    }

    public function update(User $user, RoleContract $role): bool
    {
        return $this->checkWildcardPermissions($user);
    }

    public function delete(User $user, RoleContract $role): bool
    {
        return $this->checkWildcardPermissions($user);
    }
}
