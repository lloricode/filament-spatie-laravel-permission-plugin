<?php

declare(strict_types=1);

return [
    'role_names' => [
        'web' => [
            'super_admin' => 'super_admin',
            'admin' => 'admin',
        ],
    ],
    'extra_role_names' => [
        // 'web' => [
        //    'user' => 'user',
        // ],
    ],

    'seeders' => [
        'roles' => \Lloricode\FilamentSpatieLaravelPermissionPlugin\Database\Seeders\DefaultRoleSeeder::class,
        'permissions' => \Lloricode\FilamentSpatieLaravelPermissionPlugin\Database\Seeders\DefaultPermissionSeeder::class,
    ],

    'translated' => false,
];
