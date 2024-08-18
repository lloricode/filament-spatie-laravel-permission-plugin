<?php

declare(strict_types=1);

namespace Lloricode\FilamentSpatieLaravelPermissionPlugin\Tests\Config;

use Lloricode\FilamentSpatieLaravelPermissionPlugin\Config\PermissionConfig;

use function PHPUnit\Framework\assertSame;

beforeEach(function () {

    config([
        'auth.defaults.guard' => 'default_guard',
        'auth.guards' => [
            'default_guard' => [
                'driver' => 'session',
                'provider' => 'users',
            ],
            'default_guard2' => [
                'driver' => 'session',
                'provider' => 'users',
            ],
        ],

        'filament-permission.role_names' => [
            'default_guard' => [
                'super_admin' => 'super_admin',
                'admin' => 'admin',
            ],
            'default_guard2' => [
                'super_admin' => 'super_admin',
                'admin' => 'admin',
                'super_admin2' => 'super_admin2',
                'admin2' => 'admin2',
            ],
        ],
        'filament-permission.extra_role_names' => [
            'default_guard' => [
                'extra_role' => 'extra_role',
            ],
            'default_guard2' => [
                'extra_role' => 'extra_role',
                'extra_role2' => 'extra_role2',
            ],
        ],
    ]);
});

test('default guard', function () {
    assertSame('default_guard', PermissionConfig::defaultGuardName());
});

test('get super admin role name', function () {
    assertSame('super_admin', PermissionConfig::superAdmin());
});

test('get admin role name', function () {
    assertSame('admin', PermissionConfig::admin());
});

test('get extra role name', function () {
    assertSame('extra_role', PermissionConfig::extraRole('extra_role'));
});

test('get all role names', function () {
    assertSame([
        'default_guard' => [
            'super_admin' => 'super_admin',
            'admin' => 'admin',
        ],
        'default_guard2' => [
            'super_admin' => 'super_admin',
            'admin' => 'admin',
            'super_admin2' => 'super_admin2',
            'admin2' => 'admin2',
        ],
    ], PermissionConfig::roleNames());
});

test('get all extra role names', function () {
    assertSame([
        'default_guard' => [
            'extra_role' => 'extra_role',
        ],
        'default_guard2' => [
            'extra_role' => 'extra_role',
            'extra_role2' => 'extra_role2',
        ],
    ], PermissionConfig::extraRoleNames());
});

test('get all role names group by guard name', function () {
    assertSame([
        'default_guard' => [
            'super_admin' => 'super_admin',
            'admin' => 'admin',
            'extra_role' => 'extra_role',
        ],
        'default_guard2' => [
            'super_admin' => 'super_admin',
            'admin' => 'admin',
            'super_admin2' => 'super_admin2',
            'admin2' => 'admin2',
            'extra_role' => 'extra_role',
            'extra_role2' => 'extra_role2',
        ],
    ], PermissionConfig::allRoleNamesGroupByGuardName());
});

test('get all role name by guard name', function () {
    assertSame([
        'super_admin',
        'admin',
        'extra_role',
        'super_admin2',
        'admin2',
        'extra_role2',
    ], PermissionConfig::allRoleNames());

    assertSame([
        'super_admin',
        'admin',
        'super_admin2',
        'admin2',
        'extra_role',
        'extra_role2',
    ], PermissionConfig::allRoleNames('default_guard2'));
});

test('role names by guard name', function () {

    assertSame([
        'super_admin',
        'admin',
    ], PermissionConfig::roleNamesByGuardName('default_guard'));

    assertSame([
        'super_admin',
        'admin',
        'super_admin2',
        'admin2',
    ], PermissionConfig::roleNamesByGuardName('default_guard2'));

});

test('extra role names by guard name', function () {

    assertSame([
        'extra_role',
    ], PermissionConfig::extraRoleNamesByGuardName('default_guard'));

    assertSame([
        'extra_role',
        'extra_role2',
    ], PermissionConfig::extraRoleNamesByGuardName('default_guard2'));

});

test('throw duplicate role names for guard name', function () {

    config([
        'filament-permission.role_names' => [
            'default_guard' => [
                'super_admin' => 'super_admin',
                'admin' => 'admin',
            ],
        ],
        'filament-permission.extra_role_names' => [
            'default_guard' => [
                'super_admin' => 'super_admin',
            ],
        ],
    ]);

    PermissionConfig::checkNoSameRoleNameOnExtra();
})
    ->throws('The extra_role name "super_admin" is already defined in role_names in guard "default_guard".');
test('throw guard name not exists', function () {

    config([
        'filament-permission.role_names' => [
            'xxxxx' => [
                'super_admin' => 'super_admin',
                'admin' => 'admin',
            ],
        ],
        'filament-permission.extra_role_names' => [
            'yyyyyyyyy' => [
                'super_admin' => 'super_admin',
            ],
        ],
    ]);

    PermissionConfig::checkDefaultGuardNameExist();
})
    ->throws('Guard name "yyyyyyyyy", "xxxxx" is not defined in config/auth.php.');
