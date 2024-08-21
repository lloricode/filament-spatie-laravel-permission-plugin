<?php

declare(strict_types=1);

namespace Lloricode\FilamentSpatieLaravelPermissionPlugin;

use Illuminate\Support\Facades\Gate;
use Livewire\Features\SupportTesting\Testable;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Commands\PermissionSyncCommand;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Policies\RolePolicy;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Testing\TestsFilamentSpatieLaravelPermissionPlugin;
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
            Gate::policy(config('permission.models.role'), RolePolicy::class);
        });
    }

    public function packageBooted(): void
    {
        // Testing
        Testable::mixin(new TestsFilamentSpatieLaravelPermissionPlugin);
    }
}
