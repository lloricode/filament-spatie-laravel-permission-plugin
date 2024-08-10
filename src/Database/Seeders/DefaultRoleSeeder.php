<?php

declare(strict_types=1);

namespace Lloricode\FilamentSpatieLaravelPermissionPlugin\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Spatie\Permission\Commands\CreateRole;
use Spatie\Permission\Contracts\Permission as PermissionContract;

class DefaultRoleSeeder extends Seeder
{
    public function __construct(
        protected readonly PermissionContract $permissionContract,
    ) {}

    /**
     * @throws \Exception
     */
    public function run(): void
    {
        $guard = Config::string('filament-permission.guard');
        foreach (Config::array('filament-permission.roles') as $roleName) {
            Artisan::call(CreateRole::class, [
                'name' => $roleName,
                'guard' => $guard,
                'permissions' => config('filament-permission.roles.admin') === $roleName
                    ? $this->permissionContract
                        ->where('guard_name', $guard)
                        ->pluck('name')
                        ->implode('|')
                    : null,
            ]);
        }
        foreach (Config::array('filament-permission.extra_roles') as $roleName) {
            Artisan::call(CreateRole::class, [
                'name' => $roleName,
                'guard' => $guard,
            ]);
        }

    }
}
