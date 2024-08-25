<?php

declare(strict_types=1);

namespace Lloricode\FilamentSpatieLaravelPermissionPlugin\Contracts;

interface HasPermissionUser
{
    /**
     * This will skip all gate checks
     */
    public function isSuperAdmin(?string $guardName = null): bool;

    public function isAdminOrSuperAdmin(?string $guardName = null): bool;
}
