<?php

declare(strict_types=1);

namespace Lloricode\FilamentSpatieLaravelPermissionPlugin\Tests\Resources\RoleResource\Pages;

use Lloricode\FilamentSpatieLaravelPermissionPlugin\Config\PermissionConfig;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Resources\RoleResource\Pages\EditRole;

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Livewire\livewire;

beforeEach(function () {

    loginAsSuperAdmin();
});

it('can update role name', function () {

    assertDatabaseCount('roles', 2);

    $name = fake()->word();

    $role = createRole($name);

    livewire(EditRole::class, ['record' => $role->getRouteKey()])
        ->fillForm([
            'name' => $name.' new',
            'guard_name' => PermissionConfig::defaultGuardName(),
        ])
        ->call('save')
        ->assertSuccessful();

    assertDatabaseCount('roles', 3);
    assertDatabaseHas('roles', [
        'name' => $name.' new',
    ]);
});
it('can not update admin', function () {

    assertDatabaseCount('roles', 2);

    $name = fake()->word();

    livewire(EditRole::class, ['record' => getAdminRole()->getRouteKey()])
        ->fillForm([
            'name' => $name,
            'guard_name' => PermissionConfig::defaultGuardName(),
        ])
        ->call('save')
        ->assertStatus(400);

    assertDatabaseCount('roles', 2);
});
