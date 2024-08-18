<?php

declare(strict_types=1);

namespace Lloricode\FilamentSpatieLaravelPermissionPlugin\Config;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config as ConfigFacade;

final class PermissionConfig
{
    public static function getDefaultGuardName(): string
    {
        return ConfigFacade::string('auth.defaults.guard');
    }

    public static function superAdmin(?string $guardName = null): string
    {
        $guardName ??= self::getDefaultGuardName();

        return ConfigFacade::string('filament-permission.role_names.' . $guardName . '.super_admin');
    }

    public static function admin(?string $guardName = null): string
    {
        $guardName ??= self::getDefaultGuardName();

        return ConfigFacade::string('filament-permission.role_names.' . $guardName . '.admin');
    }

    public static function extraRole(string $roleName, ?string $guardName = null): string
    {
        if (blank(self::extraRoleNames())) {
            abort(500, 'No extra roles found in config/filament-permission.php');
        }

        $guardName ??= self::getDefaultGuardName();

        return ConfigFacade::string('filament-permission.extra_roles.' . $guardName . '.' . $roleName);
    }

    /**
     * @return array<string, array<string, string>>
     */
    public static function allRoleNamesByGuardName(): array
    {
        self::checkNoSameRoleNameOnExtra();

        $roles = self::roleNames();
        $extraRoles = self::extraRoleNames();

        return array_merge_recursive($roles, $extraRoles);
    }

    /**
     * @return array<string, string>
     */
    public static function allRoleNames(?string $guardName = null): array
    {
        if ($guardName === null) {
            return Arr::flatten(self::allRoleNamesByGuardName());
        }

        return Arr::get(self::allRoleNamesByGuardName(), $guardName);
    }

    public static function allGuardNames(): array
    {
        $extraRoles = self::extraRoleNames();
        $roles = self::roleNames();

        return collect($extraRoles)->keys()
            ->merge(collect($roles)->keys())
            ->unique()
            ->toArray();
    }

    /**
     * @return array<string, array<string, string>>
     */
    private static function roleNames(): array
    {
        return ConfigFacade::array('filament-permission.role_names');
    }

    /**
     * @return array<string, array<string, string>>
     */
    private static function extraRoleNames(): array
    {
        return ConfigFacade::array('filament-permission.extra_role_names', []);
    }

    private static function checkDefaultGuardNameExist(): void
    {

        $guardNames = self::allGuardNames();
        $authGuardNames = array_keys(config('auth.guards'));

        foreach ($guardNames as $guardName) {
            if (! in_array($guardName, $authGuardNames, true)) {
                abort(500, 'Guard name "' . $guardName . '" is not defined in config/auth.php');
            }
        }

    }

    private static function checkNoSameRoleNameOnExtra(): void
    {
        self::checkDefaultGuardNameExist();
        $extraRoles = self::extraRoleNames();

        if (blank($extraRoles)) {
            return;
        }

        foreach (self::roleNames() as $guardName => $guardRoles) {

            foreach ($guardRoles as $role) {
                if (in_array($role, $extraRoles[$guardName], true)) {
                    abort(500, 'extra_role name "' . $role . '" is already defined in role_names in guard "' . $guardName . '"');
                }
            }
        }

    }
}
