<?php

declare(strict_types=1);

namespace Lloricode\FilamentSpatieLaravelPermissionPlugin\Actions;

use Illuminate\Database\Eloquent\Model;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Data\RoleData;
use Spatie\Permission\Contracts\Role as RoleContract;

readonly class CreateRoleAction
{
    public function __construct(private RoleContract $roleContract) {}

    public function execute(RoleData $roleData): RoleContract & Model
    {
        /** @var RoleContract&Model $role */
        $role = $this->roleContract->findOrCreate(
            name: $roleData->name,
            guardName: $roleData->guard_name,
        );

        $role->givePermissionTo($roleData->permissions);

        return $role;
    }
}
