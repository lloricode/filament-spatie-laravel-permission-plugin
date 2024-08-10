<?php

declare(strict_types=1);

namespace Lloricode\FilamentSpatieLaravelPermissionPlugin\Database\Seeders;

use Exception;
use Filament\Facades\Filament;
use Filament\Panel;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Contracts\HasPermissionPage;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Contracts\HasPermissionWidgets;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\FilamentPermissionGenerateName;

class DefaultPermissionSeeder extends BasePermissionSeeder
{
    /** @throws Exception */
    #[\Override]
    protected function permissionsByGuard(): array
    {
        return [
            'admin' => self::getPermissionsFromPanels()
                ->merge(self::getPermissionsFromResourceModelPolicies())
                ->merge(self::getPermissionsFromWidgets())
                ->merge(self::getPermissionsFromPages())
                ->toArray(),
        ];
    }

    /** @return \Illuminate\Support\Collection<int, string> */
    private static function getPermissionsFromPanels(): Collection
    {
        return collect(Filament::getPanels())
            ->map(fn (Panel $panel) => FilamentPermissionGenerateName::getPanelPermissionName($panel))
            ->prepend(FilamentPermissionGenerateName::PANELS)
            ->values();
    }

    /** @return \Illuminate\Support\Collection<int, string> */
    private static function getPermissionsFromResourceModelPolicies(): Collection
    {
        $permissionsByPolicy = collect();

        foreach (Filament::getResources() as $filamentResource) {

            $modelPolicy = Gate::getPolicyFor($filamentResource::getModel());

            $permissionsByPolicy = $permissionsByPolicy->merge(
                self::generateFilamentResourcePermissions($modelPolicy::class)
            );
        }

        return $permissionsByPolicy;
    }

    /** @return \Illuminate\Support\Collection<int, string> */
    private static function getPermissionsFromWidgets(): Collection
    {
        $permissionNames = collect();

        foreach (Filament::getWidgets() as $widget) {
            if (app($widget) instanceof HasPermissionWidgets) {
                $permissionNames->push(FilamentPermissionGenerateName::getWidgetPermissionName($widget));
            }
        }

        if ($permissionNames->isEmpty()) {
            return $permissionNames;
        }

        $permissionNames->prepend(FilamentPermissionGenerateName::WIDGETS);

        return $permissionNames;
    }

    /** @return \Illuminate\Support\Collection<int, string> */
    private static function getPermissionsFromPages(): Collection
    {
        $permissionNames = collect();

        foreach (Filament::getPages() as $page) {
            if (app($page) instanceof HasPermissionPage && $page::canBeSeed()) {
                $permissionNames->push(FilamentPermissionGenerateName::getPagePermissionName($page));
            }
        }

        if ($permissionNames->isEmpty()) {
            return $permissionNames;
        }

        $permissionNames->prepend(FilamentPermissionGenerateName::PAGES);

        return $permissionNames;
    }
}
