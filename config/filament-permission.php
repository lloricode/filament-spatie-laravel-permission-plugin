<?php

declare(strict_types=1);

return [
    'roles' => [
        'super_admin' => 'super_admin',
        'admin' => 'admin',
    ],
    'extra_roles' => [

    ],

    'seeders' => [
        'roles' => \Lloricode\FilamentSpatieLaravelPermissionPlugin\Database\Seeders\DefaultRoleSeeder::class,
        'permissions' => \Lloricode\FilamentSpatieLaravelPermissionPlugin\Database\Seeders\DefaultPermissionSeeder::class,
    ],
];
