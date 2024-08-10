<?php

declare(strict_types=1);

namespace Lloricode\FilamentSpatieLaravelPermissionPlugin;

use Filament\Facades\Filament;
use Filament\Panel;
use Illuminate\Support\Str;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Contracts\HasPermissionPage;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Contracts\HasPermissionWidgets;

final class FilamentPermissionGenerateName
{
    public const PANELS = 'panels';

    public const WIDGETS = 'widgets';

    public const PAGES = 'pages';

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

                return self::PANELS . '.' . $panelObject->getId();
            }
        );
    }

    /** @param  \Lloricode\FilamentSpatieLaravelPermissionPlugin\Contracts\HasPermissionPage|class-string<\Lloricode\FilamentSpatieLaravelPermissionPlugin\Contracts\HasPermissionPage>  $page */
    public static function getPagePermissionName(HasPermissionPage | string $page): string
    {
        if ($page instanceof HasPermissionPage) {
            $page = $page::class;
        }

        return once(fn () => self::PAGES . '.' . Str::of($page)->classBasename()->camel());
    }

    /** @param  \Lloricode\FilamentSpatieLaravelPermissionPlugin\Contracts\HasPermissionWidgets|class-string<\Lloricode\FilamentSpatieLaravelPermissionPlugin\Contracts\HasPermissionWidgets>  $widget */
    public static function getWidgetPermissionName(HasPermissionWidgets | string $widget): string
    {
        if ($widget instanceof HasPermissionWidgets) {
            $widget = $widget::class;
        }

        return once(fn () => self::WIDGETS . '.' . Str::of($widget)->classBasename()->camel());
    }
}
