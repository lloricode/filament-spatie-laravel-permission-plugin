<?php

declare(strict_types=1);

namespace Lloricode\FilamentSpatieLaravelPermissionPlugin\Policies;

use Illuminate\Foundation\Auth\User;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Config\PermissionConfig;
use Spatie\Permission\Contracts\Role as RoleContract;

class RolePolicy
{
    use ChecksWildcardPermissions;

    public function viewAny(User $user): bool
    {
        return $this->checkWildcardPermissions($user);
    }

    public function view(User $user, RoleContract $role): bool
    {
        if (in_array($role->name, PermissionConfig::extraRoleNamesByGuardName($role->guard_name), true)) {
            return true;
        }

        if (in_array($role->name, PermissionConfig::roleNamesByGuardName($role->guard_name), true)) {
            return false;
        }

        return $this->checkWildcardPermissions($user);
    }

    public function create(User $user): bool
    {
        return $this->checkWildcardPermissions($user);
    }

    public function update(User $user, RoleContract $role): bool
    {
        return $this->canModify($user, $role);
    }

    public function delete(User $user, RoleContract $role): bool
    {
        return $this->canModify($user, $role);
    }

    private function canModify(User $user, RoleContract $role): bool
    {
        if (in_array($role->name, PermissionConfig::allRoleNames($role->guard_name), true)) {
            return false;
        }

        return $this->checkWildcardPermissions($user);
    }
}
