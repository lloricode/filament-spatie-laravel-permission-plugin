<?php

declare(strict_types=1);

namespace Lloricode\FilamentSpatieLaravelPermissionPlugin\Config;

use Illuminate\Support\Facades\Config as ConfigFacade;

final class PermissionConfig
{
    public static function defaultGuardName(): string
    {
        return ConfigFacade::string('auth.defaults.guard');
    }

    public static function superAdmin(?string $guardName = null): string
    {
        return self::roleName('super_admin', $guardName);
    }

    public static function admin(?string $guardName = null): string
    {
        return self::roleName('admin', $guardName);
    }

    public static function roleName(string $roleKey, ?string $guardName = null): string
    {
        $guardName ??= self::defaultGuardName();

        return ConfigFacade::string('filament-permission.role_names.'.$guardName.'.'.$roleKey);
    }

    /**
     * @return array<string, array<string, string>>
     */
    public static function roleNamesGroupByGuardName(): array
    {
        return ConfigFacade::array('filament-permission.role_names');
    }

    /**
     * @return array<string, string>
     */
    public static function roleNamesByGuardName(?string $guardName = null): array
    {
        return self::roleNamesGroupByGuardName()[$guardName ?? self::defaultGuardName()];
    }

    /**
     * @return array<string, string>
     */
    public static function customPermissionsNames(?string $guardName = null): array
    {
        $guardName ??= self::defaultGuardName();

        return ConfigFacade::array('filament-permission.permission_names.'.$guardName);
    }
}
