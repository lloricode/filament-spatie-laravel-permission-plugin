<?php

declare(strict_types=1);

namespace Lloricode\FilamentSpatieLaravelPermissionPlugin\Database\Seeders;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Seeder;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Config\PermissionConfig;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Enums\PermissionType;
use Spatie\Permission\Contracts\Permission as PermissionContract;
use Spatie\Permission\Contracts\Role as RoleContract;

class DefaultRoleSeeder extends Seeder
{
    public function __construct(
        protected readonly RoleContract $roleContract,
        protected readonly PermissionContract $permissionContract,
    ) {}

    /**
     * @throws \Exception
     */
    public function run(): void
    {
        foreach (PermissionConfig::roleNamesGroupByGuardName() as $guardName => $roleNames) {

            foreach ($roleNames as $roleName) {
                $role = $this->roleContract->findOrCreate(
                    name: $roleName,
                    guardName: $guardName,
                );
                if (PermissionConfig::admin($guardName) === $roleName) {
                    $role->givePermissionTo(
                        $this->permissionContract
                            ->when(
                                ! PermissionConfig::customPermissionsNamesGivesToAdmin(),
                                fn (Builder $query) => $query
                                    ->whereNotLike('name', PermissionType::customs->value.'%')
                            )
                            ->where('guard_name', $guardName)
                            ->pluck('name')
                    );
                }
            }
        }

    }
}
