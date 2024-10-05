<?php

declare(strict_types=1);

namespace Lloricode\FilamentSpatieLaravelPermissionPlugin\Config;

use Illuminate\Support\Facades\Config;

final class PermissionConfig
{
    public static function defaultGuardName(): string
    {
        return Config::string('filament-permission.defaults.guard');
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

        return Config::string('filament-permission.role_names.'.$guardName.'.'.$roleKey);
    }

    /**
     * @return array<string, array<string, string>>
     */
    public static function roleNamesGroupByGuardName(): array
    {
        return Config::array('filament-permission.role_names');
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

        return Config::array('filament-permission.custom_permission_names.'.$guardName);
    }

    public static function translated(): bool
    {
        return Config::boolean('filament-permission.translated', false);
    }

    /**
     * @return class-string|null
     */
    public static function rolePolicy(): ?string
    {
        return config('filament-permission.model_policies.role');
    }
}
