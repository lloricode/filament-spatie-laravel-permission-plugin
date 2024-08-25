<?php

declare(strict_types=1);

namespace Lloricode\FilamentSpatieLaravelPermissionPlugin;

use Filament\Facades\Filament;
use Filament\Panel;
use Illuminate\Support\Str;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Config\PermissionConfig;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Contracts\HasPermissionPages;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Contracts\HasPermissionWidgets;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Enums\PermissionType;

final class FilamentPermissionGenerateName
{
    private function __construct() {}

    public static function getPanelPermissionName(string | Panel $panel): string
    {
        return once(
            function () use ($panel) {
                if (is_string($panel)) {
                    $panelObject = Filament::getPanel($panel);

                    if ($panelObject->getId() !== $panel) {
                        throw new \Exception('Panel [' . $panel . '] not found.');
                    }

                } else {
                    $panelObject = $panel;
                }

                return PermissionType::panels->value . '.' . $panelObject->getId();
            }
        );
    }

    /** @param  HasPermissionPages|class-string<HasPermissionPages>  $page */
    public static function getPagePermissionName(HasPermissionPages | string $page): string
    {
        if ($page instanceof HasPermissionPages) {
            $page = $page::class;
        }

        return PermissionType::pages->value . '.' . Str::of($page)->classBasename()->camel();
    }

    /** @param  HasPermissionWidgets|class-string<HasPermissionWidgets>  $widget */
    public static function getWidgetPermissionName(HasPermissionWidgets | string $widget): string
    {
        if ($widget instanceof HasPermissionWidgets) {
            $widget = $widget::class;
        }

        return PermissionType::widgets->value . '.' . Str::of($widget)->classBasename()->camel();
    }

    public static function getCustomPermissionName(string $customPermissionName): string
    {
        if (! in_array($customPermissionName, PermissionConfig::customPermissionsNames(), true)) {
            throw new \Exception('Custom permission [' . $customPermissionName . '] not found.');
        }

        return PermissionType::customs->value . '.' . $customPermissionName;
    }
}
