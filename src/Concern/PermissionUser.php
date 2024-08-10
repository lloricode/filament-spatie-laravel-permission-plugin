<?php

declare(strict_types=1);

namespace Lloricode\FilamentSpatieLaravelPermissionPlugin\Concern;

trait PermissionUser
{
    public function isAdminOrSuperAdmin(): bool
    {
        return $this->hasAnyRole(
            config('filament-permission.roles.super_admin'),
            config('filament-permission.roles.admin')
        );
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole(config('filament-permission.roles.super_admin'));
    }
}
