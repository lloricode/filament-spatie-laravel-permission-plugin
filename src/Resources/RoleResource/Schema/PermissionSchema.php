<?php

declare(strict_types=1);

namespace Lloricode\FilamentSpatieLaravelPermissionPlugin\Resources\RoleResource\Schema;

use Filament\Forms;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Support\Collection as CollectionSupport;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Resources\RoleResource\Support\PermissionData;
use Spatie\Permission\Contracts\Permission as PermissionContract;
use Spatie\Permission\Contracts\Role as RoleContract;
use Spatie\Permission\PermissionRegistrar;

final class PermissionSchema
{
    private static string $guardName;

    public static function schema(?string $guardName): array
    {
        if (blank($guardName)) {
            return [
                Forms\Components\Placeholder::make(trans('Select guard name first before selecting permissions')),
            ];
        }

        self::$guardName = $guardName;
        unset($guardName);

        if (self::permissionGroupByParent()->isEmpty()) {
            return [
                Forms\Components\Placeholder::make(trans('No Permission on guard name :value', ['value' => self::$guardName])),
            ];
        }

        return [

            Forms\Components\Hidden::make('permissions')
                ->afterStateHydrated(function (Forms\Components\Hidden $component, ?RoleContract $record): void {
                    $component->state($record ? $record->permissions->pluck('name') : []);
                })
                ->dehydrateStateUsing(
                    fn (Get $get): array => self::permissionGroupByParent()
                        ->reduce(
                            function (
                                CollectionSupport $result,
                                CollectionSupport $permissionGroup,
                                string $parentPermission
                            ) use ($get): CollectionSupport {

                                if ($get($parentPermission) ?? false) {
                                    $result->push($parentPermission);
                                } elseif (filled($names = $get("{$parentPermission}_abilities"))) {
                                    $result = $result->merge($names);
                                }

                                return $result;
                            },
                            collect()
                        )
                        ->toArray()
                ),

            Forms\Components\Toggle::make('select_all')
                ->translateLabel()
                ->onIcon('heroicon-s-shield-check')
                ->offIcon('heroicon-s-shield-exclamation')
                ->helperText(trans('Enable all Permissions for this role'))
                ->reactive()
                ->afterStateUpdated(function (Get $get, Set $set, bool $state): void {
                    self::updatedToggleSelectAllState(get: $get, set: $set, state: $state);
                })
                ->afterStateHydrated(function (Forms\Components\Toggle $component, ?RoleContract $record): void {

                    if ($record === null) {
                        $component->state(false);

                        return;
                    }

                    $all = true;

                    foreach (self::permissions() as $permissionData) {

                        if (! $record->hasPermissionTo($permissionData->name, self::$guardName)) {
                            $all = false;

                            break;
                        }
                    }

                    $component->state($all);

                })
                ->dehydrated(false),

            Forms\Components\Grid::make(['sm' => 2])
                ->schema(
                    /////////////////////////////////////////////////////////////////////////////////////////// start
                    self::permissionGroupByParent()
                        ->map(
                            fn (CollectionSupport $permissionsDatas, string $parentPermission): Forms\Components\Section => Forms\Components\Section::make()
                                ->schema([

                                    Forms\Components\Toggle::make($parentPermission)
                                        ->translateLabel(Config::boolean('filament-permission.translated', false))
                                        ->onIcon('heroicon-s-lock-open')
                                        ->offIcon('heroicon-s-lock-closed')
                                        ->reactive()
                                        ->afterStateHydrated(
                                            function (Forms\Components\Toggle $component, ?RoleContract $record) use (
                                                $parentPermission
                                            ): void {
                                                if ($record === null) {
                                                    $component->state(false);

                                                    return;
                                                }
                                                $component->state($record->hasPermissionTo($parentPermission, self::$guardName));
                                            }
                                        )
                                        ->afterStateUpdated(
                                            function (Set $set, Get $get, bool $state) use (
                                                $parentPermission,
                                                $permissionsDatas
                                            ): void {
                                                self::updatedToggleSelectParentPermissionState(
                                                    parentPermission: $parentPermission,
                                                    permissionsDatas: $permissionsDatas,
                                                    get: $get,
                                                    set: $set,
                                                    state: $state,
                                                );
                                            }
                                        )
                                        ->dehydrated(false),

                                    Forms\Components\Fieldset::make('Abilities')
                                        ->translateLabel()
                                        ->schema([
                                            /////// start CheckboxList
                                            Forms\Components\CheckboxList::make("{$parentPermission}_abilities")
                                                ->hiddenLabel()
                                                ->options(
                                                    self::parentAbilitiesLabeled(
                                                        permissionsDatas: $permissionsDatas
                                                    )
                                                        ->sort()
                                                        ->toArray()
                                                )
                                                ->columns(2)
                                                ->reactive()
                                                ->afterStateHydrated(
                                                    function (Forms\Components\CheckboxList $component, ?RoleContract $record) use (
                                                        $permissionsDatas,
                                                        $parentPermission
                                                    ): void {

                                                        if ($record === null) {
                                                            $component->state([]);

                                                            return;
                                                        }

                                                        if ($record->hasPermissionTo($parentPermission, self::$guardName)) {
                                                            $component->state($permissionsDatas->pluck('name')->toArray());

                                                            return;
                                                        }

                                                        $names = [];

                                                        foreach ($permissionsDatas as $permissionData) {

                                                            if ($record->hasPermissionTo($permissionData->name, self::$guardName)) {
                                                                $names[] = $permissionData->name;
                                                            }
                                                        }

                                                        $component->state($names);

                                                    }
                                                )
                                                ->afterStateUpdated(
                                                    function (Set $set, Get $get, CollectionSupport | array $state) use (
                                                        $parentPermission,
                                                        $permissionsDatas
                                                    ): void {
                                                        self::updatedCheckboxListPermissionState(
                                                            parentPermission: $parentPermission,
                                                            permissionDatas: $permissionsDatas,
                                                            set: $set,
                                                            get: $get,
                                                            state: $state
                                                        );
                                                    }
                                                )
                                                ->dehydrated(false),
                                            /////// end CheckboxList
                                        ])
                                        ->columnSpan(1),

                                ])
                                ->columnSpan(1)
                        )
                        ->toArray(),
                    /////////////////////////////////////////////////////////////////////////////////////////// end
                ),
        ];
    }

    /** @return CollectionSupport<int, PermissionData> */
    private static function permissions(): CollectionSupport
    {
        return once(
            fn () => app(PermissionRegistrar::class)
                ->getPermissions(['guard_name' => self::$guardName])
                ->sortBy('name')
                ->map(fn (PermissionContract $permission): PermissionData => new PermissionData($permission->name))
        );
    }

    /** @return CollectionSupport<string, CollectionSupport<int, PermissionData>> */
    private static function permissionGroupByParent(): CollectionSupport
    {
        return once(
            fn () => self::permissions()
                ->filter(
                    fn (PermissionData $permissionData): bool => ! $permissionData->is_parent
                )
                ->groupBy(
                    fn (PermissionData $permissionData): string => $permissionData->parent_name
                )
        );
    }

    /**
     * @param  CollectionSupport<int, PermissionData>  $permissionsDatas
     * @return CollectionSupport<string, string>
     */
    private static function parentAbilitiesLabeled(CollectionSupport $permissionsDatas): CollectionSupport
    {
        return $permissionsDatas
            ->mapWithKeys(function (PermissionData $permissionData) {

                $ability = $permissionData->child_name ?? throw new \ErrorException('This should not happen');

                $ability = (string) Str::of($ability)
                    ->snake(' ')
                    ->ucfirst();

                if (Config::boolean('filament-permission.translated', false)) {
                    $ability = trans($ability);
                }

                return [
                    $permissionData->name => $ability,
                ];
            });
    }

    /** @param  CollectionSupport<int, PermissionData>  $permissionDatas */

    /**
     * @param  CollectionSupport<int, PermissionData>  $permissionDatas
     * @param  CollectionSupport<int, int>|array<int, int>  $state
     */
    private static function updatedCheckboxListPermissionState(
        string $parentPermission,
        CollectionSupport $permissionDatas,
        Set $set,
        Get $get,
        CollectionSupport | array $state
    ): void {
        $set($parentPermission, $permissionDatas->count() === count($state));

        self::refreshToggleSelectAllState(get: $get, set: $set);
    }

    /** @param  CollectionSupport<int, PermissionData>  $permissionsDatas */
    private static function updatedToggleSelectParentPermissionState(
        string $parentPermission,
        CollectionSupport $permissionsDatas,
        Get $get,
        Set $set,
        bool $state
    ): void {
        $set($parentPermission . '_abilities', $get($parentPermission)
            ? self::parentAbilitiesLabeled($permissionsDatas)->keys()
            : []);

        self::refreshToggleSelectAllState(get: $get, set: $set);
    }

    private static function updatedToggleSelectAllState(Get $get, Set $set, bool $state): void
    {
        foreach (self::permissionGroupByParent() as $parentPermissionName => $permissionDatas) {
            $set($parentPermissionName, $state);
            $set(
                "{$parentPermissionName}_abilities",
                $state ? $permissionDatas
                    ->pluck('name')
                    : []
            );
        }
    }

    private static function refreshToggleSelectAllState(Get $get, Set $set): void
    {
        $selectAll = true;

        foreach (self::permissionGroupByParent() as $parentPermission => $permissionAbilities) {
            if ($get($parentPermission) === false) {
                $selectAll = false;

                break;
            }
        }

        $set('select_all', $selectAll);
    }
}
