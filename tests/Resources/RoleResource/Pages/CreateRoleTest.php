<?php

declare(strict_types=1);

namespace Lloricode\FilamentSpatieLaravelPermissionPlugin\Tests\Resources\RoleResource\Pages;

use Lloricode\FilamentSpatieLaravelPermissionPlugin\Resources\RoleResource\Pages\CreateRole;

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Livewire\livewire;

beforeEach(function () {

    loginAsSuperAdmin();
});

it('can create', function () {

    assertDatabaseCount('roles', 2);

    $name = fake()->word();

    livewire(CreateRole::class)
        ->fillForm([
            'name' => $name,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    assertDatabaseCount('roles', 3);
    assertDatabaseHas('roles', [
        'name' => $name,
    ]);
});
