<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Models\Role;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Tests\Fixture\UserFactory;
use Spatie\Permission\Contracts\Role as RoleContract;

use function Pest\Laravel\actingAs;

function loginAsSuperAdmin()
{
    $user = UserFactory::new()->createOne(['email' => 'test@test.com']);
    $user->assignRole(Role::superAdmin());
    actingAs($user);

    return $user;
}

function createRole(string $name, ?string $guard = null): RoleContract & Model
{
    /** @var RoleContract&Model $role */
    $role = app(RoleContract::class)->findOrCreate(
        name: $name,
        guardName: $guard ?? config('filament-permission.guard'),
    );

    return $role;
}
