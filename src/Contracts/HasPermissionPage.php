<?php

declare(strict_types=1);

namespace Lloricode\FilamentSpatieLaravelPermissionPlugin\Contracts;

interface HasPermissionPage
{
    public static function canBeSeed(): bool;
}
