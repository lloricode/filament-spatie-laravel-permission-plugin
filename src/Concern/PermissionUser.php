<?php

declare(strict_types=1);

namespace Lloricode\FilamentSpatieLaravelPermissionPlugin\Concern;

use Filament\Panel;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Config\PermissionConfig;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\FilamentPermissionGenerateName;

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

    /**
     * @throws \Exception
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->can(FilamentPermissionGenerateName::getPanelPermissionName($panel));
    }
}
