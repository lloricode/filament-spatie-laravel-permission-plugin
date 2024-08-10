<?php

declare(strict_types=1);

namespace Lloricode\FilamentSpatieLaravelPermissionPlugin\Actions;

use Illuminate\Database\Eloquent\Model;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Data\RoleData;
use Spatie\Permission\Contracts\Role as RoleContract;
use Spatie\Permission\PermissionRegistrar;

final readonly class CreateRoleAction
{
    public function __construct(private PermissionRegistrar $permissionRegistrar) {}

    public function execute(RoleData $roleData): RoleContract & Model
    {
        /** @var RoleContract&Model $role */
        $role = $this->permissionRegistrar->getRoleClass()::create([
            'name' => $roleData->name,
            'guard_name' => $roleData->guard_name,
        ]);

        $role->syncPermissions($roleData->permissions);

        return $role;
    }
}
