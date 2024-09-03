<?php

declare(strict_types=1);

namespace Lloricode\FilamentSpatieLaravelPermissionPlugin;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Gate;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Commands\PermissionSyncCommand;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Config\PermissionConfig;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Contracts\HasPermissionUser;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentSpatieLaravelPermissionPluginServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package->name('filament-permission')
            ->hasCommands([
                PermissionSyncCommand::class,
            ])
            ->hasConfigFile();
    }

    public function packageRegistered(): void
    {
        $this->booting(function () {
            $rolePolicy = PermissionConfig::rolePolicy();

            if ($rolePolicy !== null) {
                Gate::policy(config('permission.models.role'), $rolePolicy);
            }
        });
    }

    public function packageBooted(): void
    {
        Gate::after(function (Authenticatable $user) {

            if ($user instanceof HasPermissionUser && $user->isSuperAdmin()) {
                /** @see https://freek.dev/1325-when-to-use-gateafter-in-laravel */
                return true;
            }

            return null;
        });

    }
}
