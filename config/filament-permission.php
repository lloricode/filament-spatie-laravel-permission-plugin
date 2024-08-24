<?php

declare(strict_types=1);

return [
    /**
     * You can add as many guards as you want with as many role names as you want,
     * as long as super admin and admin are required.
     *
     * role_names.*.super_admin is required
     * role_names.*.admin is required
     */
    'role_names' => [
        // keyed by guard name
        'web' => [
            // required this cannot rename or delete or modify permissions
            'super_admin' => 'super_admin', // no permission attached to this role, but it always skips gate checks
            'admin' => 'admin', // all permissions attached to this role

            //  as many as you want, below it can edit permissions but cannot rename or delete role names
            // sample 'user' => 'user',
        ],
    ],

    'seeders' => [
        'roles' => \Lloricode\FilamentSpatieLaravelPermissionPlugin\Database\Seeders\DefaultRoleSeeder::class,
        'permissions' => \Lloricode\FilamentSpatieLaravelPermissionPlugin\Database\Seeders\DefaultPermissionSeeder::class,
    ],

    'translated' => false,
];
