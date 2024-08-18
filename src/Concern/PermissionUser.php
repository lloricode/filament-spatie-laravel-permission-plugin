<?php

declare(strict_types=1);

namespace Lloricode\FilamentSpatieLaravelPermissionPlugin\Concern;

use Lloricode\FilamentSpatieLaravelPermissionPlugin\Config\PermissionConfig;

trait PermissionUser
{
    public function isAdminOrSuperAdmin(?string $guardName = null): bool
    {
        return $this->hasAnyRole(
            PermissionConfig::superAdmin($guardName),
            PermissionConfig::admin($guardName),
        );
    }

    public function isSuperAdmin(?string $guardName = null): bool
    {
        return $this->hasRole(PermissionConfig::superAdmin($guardName));
    }
}
