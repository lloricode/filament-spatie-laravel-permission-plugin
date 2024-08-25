<?php

declare(strict_types=1);

namespace Lloricode\FilamentSpatieLaravelPermissionPlugin\Resources\RoleResource\Schema;

use Filament\Forms;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Support\Collection as CollectionSupport;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Data\PermissionData;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Enums\PermissionType;
use Spatie\Permission\Contracts\Role as RoleContract;

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

        if (PermissionCollection::groupByTypeThenParent(self::$guardName)->isEmpty()) {
            return [
                Forms\Components\Placeholder::make(trans('No Permission on guard name :guard_name', ['guard_name' => self::$guardName])),
            ];
        }

        return [

            Forms\Components\Hidden::make('permissions')
                ->afterStateHydrated(function (Forms\Components\Hidden $component, ?RoleContract $record): void {
                    $component->state($record ? $record->permissions->pluck('name') : []);
                })
                ->dehydrateStateUsing(
                    function (Get $get): array {
                        $permissionNames = [];

                        foreach (PermissionType::cases() as $permissionType) {
                            foreach (PermissionCollection::groupByTypeThenParent(self::$guardName)[$permissionType->value] ?? [] as $parentPermission => $permissionsDatas) {

                                if (blank($names = $get("{$parentPermission}_abilities"))) {
                                    continue;
                                }

                                if (count($names) === count($permissionsDatas)) {
                                    $permissionNames[] = $parentPermission;

                                    continue;
                                }

                                $permissionNames = array_merge($permissionNames, $names);

                            }
                        }

                        return $permissionNames;
                    }
                ),

            Forms\Components\Toggle::make('select_all')
                ->translateLabel()
                ->onIcon('heroicon-s-shield-check')
                ->offIcon('heroicon-s-shield-exclamation')
                ->helperText(trans('Enable all Permissions for this role'))
                ->reactive()
                ->dehydrated(false)
                ->afterStateUpdated(function (Get $get, Set $set, bool $state): void {

                    foreach (PermissionType::cases() as $permissionType) {
                        foreach (PermissionCollection::groupByTypeThenParent(self::$guardName)[$permissionType->value] ?? [] as $parentPermission => $permissionsDatas) {
                            $set("{$parentPermission}_abilities", $state ? $permissionsDatas->pluck('name')->toArray() : []);
                        }
                    }

                })
                ->afterStateHydrated(function (Forms\Components\Toggle $component, ?RoleContract $record, Set $set, Get $get): void {

                    //                    self::refreshToggleSelectAllState(record: $record, set: $set, get: $get);

                    if ($record === null) {
                        $set('select_all', false);

                        return;
                    }

                    $all = true;

                    foreach (PermissionType::cases() as $permissionType) {
                        foreach (PermissionCollection::groupByTypeThenParent(self::$guardName)[$permissionType->value] ?? [] as $parentPermission => $permissionsDatas) {

                            if (! $record->hasPermissionTo($parentPermission, self::$guardName)) {
                                $all = false;

                                break;
                            }

                        }

                        if ($all === false) {
                            break;
                        }
                    }

                    $set('select_all', $all);
                }),

            Forms\Components\Tabs::make()
                ->tabs([
                    Forms\Components\Tabs\Tab::make(trans('Resources'))
//                        ->badge(111)
                        ->schema([
                            Forms\Components\Grid::make()
                                ->schema(function () {
                                    $return = [];

                                    foreach (PermissionCollection::groupByTypeThenParent(self::$guardName)[PermissionType::resources->value] ?? [] as $parentPermission => $permissionsDatas) {
                                        $return[] = self::resourceAbilities($permissionsDatas, $parentPermission);
                                    }

                                    return $return;
                                }),
                        ]),
                    Forms\Components\Tabs\Tab::make(trans('Pages'))
                        ->schema(function () {
                            $return = [];

                            foreach (PermissionCollection::groupByTypeThenParent(self::$guardName)[PermissionType::pages->value] ?? [] as $parentPermission => $permissionsDatas) {
                                $return[] = self::abilities($permissionsDatas, $parentPermission);
                            }

                            return $return;
                        }),
                    Forms\Components\Tabs\Tab::make(trans('Widgets'))
                        ->schema(function () {
                            $return = [];

                            foreach (PermissionCollection::groupByTypeThenParent(self::$guardName)[PermissionType::widgets->value] ?? [] as $parentPermission => $permissionsDatas) {
                                $return[] = self::abilities($permissionsDatas, $parentPermission);
                            }

                            return $return;
                        }),
                    Forms\Components\Tabs\Tab::make(trans('Custom permissions'))
                        ->schema(function () {
                            $return = [];

                            foreach (PermissionCollection::groupByTypeThenParent(self::$guardName)[PermissionType::customs->value] ?? [] as $parentPermission => $permissionsDatas) {
                                $return[] = self::abilities($permissionsDatas, $parentPermission);
                            }

                            return $return;
                        }),
                ]),

        ];
    }

    /**
     * @param  CollectionSupport<int, PermissionData>  $permissionsDatas
     */
    private static function resourceAbilities(CollectionSupport $permissionsDatas, string $parentPermission): Forms\Components\Section
    {
        return Forms\Components\Section::make(Str::headline($parentPermission))
            ->translateLabel()
//            ->description('')
            ->collapsible()
            ->compact()
            ->schema([

                Forms\Components\CheckboxList::make("{$parentPermission}_abilities")
                    ->hiddenLabel()
                    ->bulkToggleable()
                    ->columns(4)
                    ->reactive()
                    ->dehydrated(false)
                    ->options(
                        self::parentAbilitiesLabeled(
                            permissionsDatas: $permissionsDatas
                        )
                    )
                    ->afterStateHydrated(
                        function (Forms\Components\CheckboxList $component, ?RoleContract $record) use (
                            $permissionsDatas,
                            $parentPermission
                        ): void {

                            self::afterStateHydratedAbilities(
                                component: $component,
                                record: $record,
                                permissionsDatas: $permissionsDatas,
                                parentPermission: $parentPermission,
                            );

                        }
                    )
                    ->afterStateUpdated(
                        function (Set $set, Get $get, RoleContract $record): void {
                            self::refreshToggleSelectAllState(
                                record: $record,
                                set: $set,
                                get: $get
                            );
                        }
                    ),

            ]);
    }

    /**
     * @param  CollectionSupport<int, PermissionData>  $permissionsDatas
     */
    private static function abilities(CollectionSupport $permissionsDatas, string $parentPermission): Forms\Components\CheckboxList
    {
        return Forms\Components\CheckboxList::make("{$parentPermission}_abilities")
            ->hiddenLabel()
            ->bulkToggleable()
            ->columns(4)
            ->reactive()
            ->dehydrated(false)
            ->options(
                self::parentAbilitiesLabeled(
                    permissionsDatas: $permissionsDatas
                )
            )
            ->afterStateHydrated(
                function (Forms\Components\CheckboxList $component, ?RoleContract $record) use (
                    $permissionsDatas,
                    $parentPermission
                ): void {

                    self::afterStateHydratedAbilities(
                        component: $component,
                        record: $record,
                        permissionsDatas: $permissionsDatas,
                        parentPermission: $parentPermission,
                    );

                }
            )
            ->afterStateUpdated(
                function (Set $set, Get $get, RoleContract $record): void {
                    self::refreshToggleSelectAllState(
                        record: $record,
                        set: $set,
                        get: $get
                    );
                }
            );
    }

    /**
     * @param  CollectionSupport<int, PermissionData>  $permissionsDatas
     */
    private static function afterStateHydratedAbilities(
        Forms\Components\CheckboxList $component,
        ?RoleContract $record,
        CollectionSupport $permissionsDatas,
        string $parentPermission
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

    private static function refreshToggleSelectAllState(?RoleContract $record, Set $set, Get $get): void
    {

        if ($record === null) {
            $set('select_all', false);

            return;
        }

        $all = true;

        foreach (PermissionType::cases() as $permissionType) {

            foreach (PermissionCollection::groupByTypeThenParent(self::$guardName)[$permissionType->value] ?? [] as $parentPermission => $permissionsDatas) {

                if (blank($names = $get("{$parentPermission}_abilities"))) {
                    $all = false;

                    break;
                }

                if (count($names) === count($permissionsDatas)) {
                    continue;
                }

                $all = false;

                break;
            }

            if ($all === false) {
                break;
            }
        }

        $set('select_all', $all);
    }

    /**
     * @param  CollectionSupport<int, PermissionData>  $permissionsDatas
     */
    private static function parentAbilitiesLabeled(CollectionSupport $permissionsDatas): array
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
            })
            ->sort()
            ->toArray();
    }
}
