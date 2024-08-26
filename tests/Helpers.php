<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Config\PermissionConfig;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Tests\Fixture\UserFactory;
use Spatie\Permission\Contracts\Role as RoleContract;

use function Pest\Laravel\actingAs;

function loginAsSuperAdmin()
{
    $user = UserFactory::new()->createOne(['email' => 'test@test.com']);
    $user->assignRole(getSuperAdminRole());
    actingAs($user);

    return $user;
}

function getSuperAdminRole(): RoleContract&Model
{
    /** @var RoleContract&Model $role */
    $role = app(RoleContract::class)->findByName(
        name: PermissionConfig::superAdmin(),
    );

    return $role;
}

function getAdminRole(): RoleContract&Model
{
    /** @var RoleContract&Model $role */
    $role = app(RoleContract::class)->findByName(
        name: PermissionConfig::admin(),
    );

    return $role;
}

function createRole(string $name, ?string $guard = null): RoleContract&Model
{
    /** @var RoleContract&Model $role */
    $role = app(RoleContract::class)->findOrCreate(
        name: $name,
        guardName: $guard ?? config('filament-permission.guard'),
    );

    return $role;
}
