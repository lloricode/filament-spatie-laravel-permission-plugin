<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Artisan;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Models\Role;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Tests\Fixture\UserFactory;
use Spatie\Permission\Commands\CreateRole;
use Spatie\Permission\Contracts\Role as RoleContract;
use Spatie\Permission\PermissionRegistrar;

use function Pest\Laravel\actingAs;

function loginAsSuperAdmin()
{
    $user = UserFactory::new()->createOne(['email' => 'test@test.com']);
    $user->assignRole(Role::superAdmin());
    actingAs($user);

    return $user;
}

function createRole(string $name, ?string $guard = null): RoleContract
{
    Artisan::call(CreateRole::class, [
        'name' => $name,
        'guard' => $guard ?? config('filament-permission.guard'),
    ]);

    return registrarRole()::findByName($name);
}

function registrarRole(): RoleContract
{
    return app(app(PermissionRegistrar::class)->getRoleClass());
}
