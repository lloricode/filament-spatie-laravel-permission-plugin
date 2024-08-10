<?php

declare(strict_types=1);

use Filament\Actions\DeleteAction;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Resources\RoleResource;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Tests\Fixture\Models\Role;

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertModelMissing;
use function Pest\Laravel\get;
use function Pest\Livewire\livewire;

beforeEach(function () {

    loginAsSuperAdmin();
});

it('can render index', function () {
    get(RoleResource::getUrl())
        ->assertOk();
});

it('can index list', function () {

    assertDatabaseCount('roles', 2);

    $roles = Role::get();

    livewire(RoleResource\Pages\ListRoles::class)
        ->assertCountTableRecords(2)
        ->assertCanSeeTableRecords($roles);
});

it('can delete', function () {
    $role = createRole(fake()->word());

    livewire(RoleResource\Pages\ListRoles::class)
        ->assertCanSeeTableRecords([$role])
        ->callTableAction(DeleteAction::class, $role);

    assertModelMissing($role);
});
