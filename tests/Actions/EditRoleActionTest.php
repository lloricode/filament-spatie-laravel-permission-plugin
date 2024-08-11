<?php

declare(strict_types=1);

namespace Lloricode\FilamentSpatieLaravelPermissionPlugin\Tests\Actions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Actions\EditRoleAction;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Data\RoleData;
use Spatie\Permission\Contracts\Role;
use Spatie\Permission\Contracts\Role as RoleContract;

use function PHPUnit\Framework\assertTrue;

it('can not edit defaults', function () {
    $roleNames = array_values(array_merge(
        Config::array('filament-permission.roles'),
        Config::array('filament-permission.extra_roles')
    ));

    /** @var array<int, Role&Model> $roles */
    $roles = app(RoleContract::class)::whereIn('name', $roleNames)->get();

    $action = app(EditRoleAction::class);

    foreach ($roles as $role) {

        try {
            $action->execute($role, new RoleData(name: fake()->word(), guard_name: '', permissions: []));

            assertTrue(false);
        } catch (\Exception $e) {
            assertTrue(true);
        }
    }

});
