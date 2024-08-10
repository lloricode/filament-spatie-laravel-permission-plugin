<?php

declare(strict_types=1);

namespace Lloricode\FilamentSpatieLaravelPermissionPlugin\Actions;

use Lloricode\FilamentSpatieLaravelPermissionPlugin\Data\RoleData;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Models\Role;

final readonly class EditRoleAction
{
    public function execute(Role $role, RoleData $roleData): Role
    {
        if (in_array($role->name, (array) config('filament-permission.roles'), true)) {
            abort(400, trans('Cannot update this role.'));
        }

        $role->update([
            'name' => $roleData->name,
            'guard_name' => $roleData->guard_name,
        ]);

        $role->syncPermissions($roleData->permissions);

        return $role;
    }
}
