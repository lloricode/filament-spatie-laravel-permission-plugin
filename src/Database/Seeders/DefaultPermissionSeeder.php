<?php

declare(strict_types=1);

namespace Lloricode\FilamentSpatieLaravelPermissionPlugin\Database\Seeders;

use Exception;
use Filament\Facades\Filament;
use Filament\Panel;
use Illuminate\Support\Facades\Gate;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Config\PermissionConfig;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Contracts\HasPermissionPages;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Contracts\HasPermissionWidgets;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Database\Seeders\Support\PermissionSeeder;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Database\Seeders\Support\ResourceSeeder;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Enums\PermissionType;
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
            PermissionConfig::defaultGuardName() => new PermissionSeeder(
                resources: $this->getPermissionsFromResourceModelPolicies(),
                panels: $this->getPermissionsFromPanels(),
                pages: $this->getPermissionsFromPages(),
                widgets: $this->getPermissionsFromWidgets(),
                customs: self::getCustomPermissionNames()
            ),
        ];
    }

    /** @return array<int, string> */
    protected function getPermissionsFromPanels(): array
    {
        return collect(Filament::getPanels())
            ->map(fn (Panel $panel) => FilamentPermissionGenerateName::getPanelPermissionName($panel))
            ->prepend(PermissionType::panels->value)
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

        $permissionNames->prepend(PermissionType::widgets->value);

        return $permissionNames->sort()->toArray();
    }

    /** @return array<int, string> */
    private static function getPermissionsFromPages(): array
    {
        $permissionNames = collect();

        foreach (Filament::getPages() as $page) {
            if (app($page) instanceof HasPermissionPages && $page::canBeSeed()) {
                $permissionNames->push(FilamentPermissionGenerateName::getPagePermissionName($page));
            }
        }

        if ($permissionNames->isEmpty()) {
            return [];
        }

        $permissionNames->prepend(PermissionType::pages->value);

        return $permissionNames->sort()->toArray();
    }

    /** @return array<int, string> */
    protected function getCustomPermissionNames(): array
    {
        $customs = collect(PermissionConfig::customPermissionsNames())
            ->map(fn (string $custom) => FilamentPermissionGenerateName::getCustomPermissionName($custom));

        if ($customs->isEmpty()) {
            return [];
        }

        return $customs->prepend(PermissionType::customs->value)
            ->values()
            ->sort()
            ->toArray();
    }
}
