<?php

declare(strict_types=1);

namespace Lloricode\FilamentSpatieLaravelPermissionPlugin\Policies;

use Illuminate\Foundation\Auth\User;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Config\PermissionConfig;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Contracts\HasPermissionUser;
use Spatie\Permission\Contracts\Role as RoleContract;

class RolePolicy
{
    use ChecksWildcardPermissions;

    public function before(?User $user, string $ability, mixed $role = null): ?bool
    {
        if ($role instanceof RoleContract) {

            if ($role->name === PermissionConfig::superAdmin($role->guard_name)) {
                return false;
            }

        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return $this->checkWildcardPermissions($user);
    }

    public function view(User $user, RoleContract $role): bool
    {
        return $this->checkWildcardPermissions($user);
    }

    public function create(User $user): bool
    {
        return $this->checkWildcardPermissions($user);
    }

    public function update(User&HasPermissionUser $user, RoleContract $role): bool
    {
        if (! $user->isSuperAdmin() && $role->name === PermissionConfig::superAdmin($role->guard_name)) {
            return false;
        }

        return $this->checkWildcardPermissions($user);
    }

    public function delete(User $user, RoleContract $role): bool
    {

        if (in_array($role->name, PermissionConfig::roleNamesByGuardName($role->guard_name), true)) {
            return false;
        }

        return $this->checkWildcardPermissions($user);
    }
}
