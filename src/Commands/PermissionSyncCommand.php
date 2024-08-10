<?php

declare(strict_types=1);

namespace Lloricode\FilamentSpatieLaravelPermissionPlugin\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Database\Seeders\DefaultPermissionSeeder;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Database\Seeders\DefaultRoleSeeder;
use Spatie\Permission\PermissionRegistrar;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand('permission:sync')]
class PermissionSyncCommand extends Command
{
    public function handle(): int
    {
        $this->call('db:seed', [
            '--class' => Config::string('filament-permission.seeders.permissions', DefaultPermissionSeeder::class),
            '--force' => true,
        ]);

        $this->call('db:seed', [
            '--class' => Config::string('filament-permission.seeders.roles', DefaultRoleSeeder::class),
            '--force' => true,
        ]);

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->components->info('Done seeding roles and permissions.');

        return self::SUCCESS;
    }
}
