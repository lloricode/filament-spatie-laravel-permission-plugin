<?php

declare(strict_types=1);

namespace Lloricode\FilamentSpatieLaravelPermissionPlugin\Tests\Actions;

use Illuminate\Database\Eloquent\Model;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Actions\EditRoleAction;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Config\PermissionConfig;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Data\RoleData;
use Spatie\Permission\Contracts\Role;
use Spatie\Permission\Contracts\Role as RoleContract;

use function PHPUnit\Framework\assertTrue;

it('can not edit defaults', function () {

    /** @var array<int, Role&Model> $roles */
    $roles = app(RoleContract::class)::whereIn('name', PermissionConfig::allRoleNames())->get();

    $action = app(EditRoleAction::class);

    foreach ($roles as $role) {

        try {
            $action->execute($role, new RoleData(name: fake()->word(), guard_name: null, permissions: []));

            assertTrue(false);
        } catch (\Exception $e) {
            assertTrue(true);
        }
    }

});
