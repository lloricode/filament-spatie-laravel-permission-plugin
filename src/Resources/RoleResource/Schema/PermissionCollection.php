<?php

declare(strict_types=1);

namespace Lloricode\FilamentSpatieLaravelPermissionPlugin\Resources\RoleResource\Schema;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection as CollectionSupport;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Data\PermissionData;
use Spatie\Permission\Contracts\Permission as PermissionContract;
use Spatie\Permission\PermissionRegistrar;

final class PermissionCollection
{
    private function __construct() {}

    /** @return CollectionSupport<int, PermissionData> */
    private static function permissions(string $guardName): CollectionSupport
    {
        return once(
            fn () => app(PermissionRegistrar::class)
                ->getPermissions(['guard_name' => $guardName])
                ->sortBy('name')
                ->map(fn (PermissionContract $permission): PermissionData => new PermissionData($permission->name))
        );
    }

    /** @return CollectionSupport<string, CollectionSupport<int, PermissionData>> */
    private static function groupByType(string $guardName): CollectionSupport
    {
        return self::permissions($guardName)
            ->groupBy(
                fn (PermissionData $permissionData): string => $permissionData->type->value
            );
    }

    /** @return CollectionSupport<string, CollectionSupport<string, CollectionSupport<int, PermissionData>>> */
    public static function groupByTypeThenParent(string $guardName): CollectionSupport
    {
        /** @phpstan-ignore return.type */
        return self::groupByType($guardName)
            ->mapWithKeys(
                fn (CollectionSupport $permissionDatas, string $type): array => [
                    $type => $permissionDatas
                        ->filter(
                            fn (PermissionData $permissionData): bool => ! $permissionData->is_parent
                        )
                        ->groupBy(
                            fn (PermissionData $permissionData): string => $permissionData->parent_name
                        ),
                ]
            );
    }
}
