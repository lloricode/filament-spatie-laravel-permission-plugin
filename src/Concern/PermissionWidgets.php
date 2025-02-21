<?php

declare(strict_types=1);

namespace Lloricode\FilamentSpatieLaravelPermissionPlugin\Concern;

use Filament\Facades\Filament;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\FilamentPermissionGenerateName;

/** @phpstan-ignore trait.unused */
trait PermissionWidgets
{
    public static function canView(): bool
    {
        return Filament::auth()->user()?->can(FilamentPermissionGenerateName::getWidgetPermissionName(static::class)) ?? false;
    }
}
