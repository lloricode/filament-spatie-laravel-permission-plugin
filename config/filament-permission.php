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
        // format snake_case
        'web' => [

            // required this cannot rename or delete or modify permissions
            /**
             * no permission attached to this role, but it always skips gate checks
             * see https://freek.dev/1325-when-to-use-gateafter-in-laravel
             */
            'super_admin' => 'super_admin',

            /**
             * all permissions attached to this role
             */
            'admin' => 'admin',

            //  as many as you want, below it can edit permissions but cannot rename or delete role names
            // sample 'user' => 'user',
        ],
    ],

    'custom_permission_names' => [
        // keyed by guard name
        // format camelCase
        'web' => [
            // 'viewLogViewer' => 'viewLogViewer',
            // 'viewPulse' => 'viewPulse',
            // 'downloadBackup' => 'downloadBackup',
            // 'deleteBackup' => 'deleteBackup',
        ],
    ],

    /**
     * You can use this seeder class to your own project level seeder.
     * But this is also able to sync your adjusted permissions name by using the ready made artisan command
     *
     * `php artisan permission:sync`
     */
    'seeders' => [

        'roles' => Lloricode\FilamentSpatieLaravelPermissionPlugin\Database\Seeders\DefaultRoleSeeder::class,

        /**
         * All permissions are generated base on your your setup.
         *
         * - public methods from Model policies class from your filament resources.
         * - filament pages that's implements `\Lloricode\FilamentSpatieLaravelPermissionPlugin\Contracts\HasPermissionPages`.
         * - filament widgets that's implements `\Lloricode\FilamentSpatieLaravelPermissionPlugin\Contracts\HasPermissionWidgets`.
         * - from this config key `custom_permission_names`
         */
        'permissions' => Lloricode\FilamentSpatieLaravelPermissionPlugin\Database\Seeders\DefaultPermissionSeeder::class,
    ],

    'model_policies' => [
        'role' => Lloricode\FilamentSpatieLaravelPermissionPlugin\Policies\RolePolicy::class,
    ],

    'translated' => false,
];
