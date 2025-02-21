<?php

declare(strict_types=1);

namespace Lloricode\FilamentSpatieLaravelPermissionPlugin\Concern;

use Filament\Facades\Filament;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\FilamentPermissionGenerateName;

/** @phpstan-ignore trait.unused */
trait PermissionPages
{
    public static function canAccess(): bool
    {
        return Filament::auth()->user()?->can(FilamentPermissionGenerateName::getPagePermissionName(static::class)) ?? false;
    }

    public static function canBeSeed(): bool
    {
        return true;
    }
}
