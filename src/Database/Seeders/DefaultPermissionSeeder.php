<?php

declare(strict_types=1);

namespace Lloricode\FilamentSpatieLaravelPermissionPlugin\Database\Seeders;

use Exception;
use Filament\Facades\Filament;
use Filament\Panel;
use Illuminate\Support\Facades\Gate;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Config\PermissionConfig;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Contracts\HasPermissionPage;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Contracts\HasPermissionWidgets;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Database\Seeders\Support\PermissionSeeder;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Database\Seeders\Support\ResourceSeeder;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\FilamentPermissionGenerateName;

class DefaultPermissionSeeder extends BasePermissionSeeder
{
    /**
     * {@inheritdoc}
     *
     * @throws Exception
     */
    #[\Override]
    protected function permissionsByGuard(): array
    {
        return [
            PermissionConfig::getDefaultGuardName() => new PermissionSeeder(
                panels: $this->getPermissionsFromPanels(),
                pages: $this->getPermissionsFromPages(),
                widgets: $this->getPermissionsFromWidgets(),
                resources: $this->getPermissionsFromResourceModelPolicies()
            ),
        ];
    }

    /** @return array<int, string> */
    protected function getPermissionsFromPanels(): array
    {
        return collect(Filament::getPanels())
            ->map(fn (Panel $panel) => FilamentPermissionGenerateName::getPanelPermissionName($panel))
            ->prepend(FilamentPermissionGenerateName::PANELS)
            ->values()
            ->sort()
            ->toArray();
    }

    /** @return array<int, ResourceSeeder> */
    protected function getPermissionsFromResourceModelPolicies(): array
    {
        $permissionsByPolicy = collect();

        foreach (Filament::getResources() as $filamentResource) {

            $modelPolicy = Gate::getPolicyFor($filamentResource::getModel());

            if ($modelPolicy === null) {
                $output = $this->command->getOutput();
                $output->warning(sprintf(
                    'Resource [%s] does not have a policy for model [%s].',
                    $filamentResource,
                    $filamentResource::getModel()
                ));

                continue;
            }

            $permissionsByPolicy->push(new ResourceSeeder(
                resource: $filamentResource,
                model: $filamentResource::getModel(),
                modelPolicy: $modelPolicy::class,
                permissionNames: self::generateFilamentResourcePermissions($modelPolicy::class)
            ));

        }

        return $permissionsByPolicy->sort()->toArray();
    }

    /** @return array<int, string> */
    protected function getPermissionsFromWidgets(): array
    {
        $permissionNames = collect();

        foreach (Filament::getWidgets() as $widget) {
            if (app($widget) instanceof HasPermissionWidgets) {
                $permissionNames->push(FilamentPermissionGenerateName::getWidgetPermissionName($widget));
            }
        }

        if ($permissionNames->isEmpty()) {
            return [];
        }

        $permissionNames->prepend(FilamentPermissionGenerateName::WIDGETS);

        return $permissionNames->sort()->toArray();
    }

    /** @return array<int, string> */
    private static function getPermissionsFromPages(): array
    {
        $permissionNames = collect();

        foreach (Filament::getPages() as $page) {
            if (app($page) instanceof HasPermissionPage && $page::canBeSeed()) {
                $permissionNames->push(FilamentPermissionGenerateName::getPagePermissionName($page));
            }
        }

        if ($permissionNames->isEmpty()) {
            return [];
        }

        $permissionNames->prepend(FilamentPermissionGenerateName::PAGES);

        return $permissionNames->sort()->toArray();
    }
}
