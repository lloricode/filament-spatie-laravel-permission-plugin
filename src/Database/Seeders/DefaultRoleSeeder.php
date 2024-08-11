<?php

declare(strict_types=1);

namespace Lloricode\FilamentSpatieLaravelPermissionPlugin\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Config;
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
        $guard = Config::string('filament-permission.guard');

        foreach (Config::array('filament-permission.roles') as $roleName) {

            $role = $this->roleContract->findOrCreate(
                name: $roleName,
                guardName: $guard,
            );

            if (config('filament-permission.roles.admin') === $roleName) {
                $role->givePermissionTo(
                    $this->permissionContract
                        ->where('guard_name', $guard)
                        ->pluck('name')
                );
            }
        }

        foreach (Config::array('filament-permission.extra_roles') as $roleName) {
            $this->roleContract->findOrCreate(
                name: $roleName,
                guardName: $guard,
            );
        }

    }
}
