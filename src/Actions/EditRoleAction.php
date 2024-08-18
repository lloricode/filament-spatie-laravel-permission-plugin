<?php

declare(strict_types=1);

namespace Lloricode\FilamentSpatieLaravelPermissionPlugin\Actions;

use Illuminate\Database\Eloquent\Model;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Config\PermissionConfig;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Data\RoleData;
use Spatie\Permission\Contracts\Role as RoleContract;

readonly class EditRoleAction
{
    public function execute(RoleContract & Model $role, RoleData $roleData): RoleContract & Model
    {
        $roleNames = PermissionConfig::allRoleNames($roleData->guard_name);

        if (in_array($role->name, $roleNames, true)) {
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
