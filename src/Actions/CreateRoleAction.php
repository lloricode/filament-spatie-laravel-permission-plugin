<?php

declare(strict_types=1);

namespace Lloricode\FilamentSpatieLaravelPermissionPlugin\Actions;

use Lloricode\FilamentSpatieLaravelPermissionPlugin\Data\RoleData;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Models\Role;

final readonly class CreateRoleAction
{
    public function execute(RoleData $roleData): Role
    {
        /** @var Role $role */
        $role = Role::create([
            'name' => $roleData->name,
            'guard_name' => $roleData->guard_name,
        ]);

        $role->syncPermissions($roleData->permissions);

        return $role;
    }
}
