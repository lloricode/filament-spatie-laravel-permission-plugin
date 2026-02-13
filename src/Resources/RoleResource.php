<?php

declare(strict_types=1);

namespace Lloricode\FilamentSpatieLaravelPermissionPlugin\Resources;

use BackedEnum;
use Exception;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Config\PermissionConfig;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Resources\RoleResource\Schema\PermissionSchema;
use Spatie\Permission\Contracts\Role as RoleContract;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RoleResource extends Resource
{
    #[\Override]
    public static function getModel(): string
    {
        /** @var class-string<Permission> $model */
        $model = app(PermissionRegistrar::class)
            ->getRoleClass();

        return $model;
    }

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldCheck;

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'name';

    #[\Override]
    public static function getNavigationGroup(): ?string
    {
        return trans('Access');
    }

    #[\Override]
    public static function form(Schemas\Schema $schema): Schemas\Schema
    {

        $readOnly = function (?RoleContract $record): bool {

            if ($record === null) {
                return false;
            }

            return in_array(
                $record->name,
                PermissionConfig::roleNamesByGuardName($record->guard_name),
                true
            );
        };

        return $schema
            ->schema([
                Schemas\Components\Section::make()
                    ->schema([
                        Schemas\Components\Grid::make()
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->translateLabel()
                                    ->required()
                                    ->string()
                                    ->maxLength(50)
                                    ->unique(ignoreRecord: true)
                                    ->readOnly($readOnly),

                                Forms\Components\Select::make('guard_name')
                                    ->translateLabel()
                                    ->required()
                                    ->disabled($readOnly)
                                    ->in(fn () => collect([config()->string('auth.defaults.guard')]))
                                    ->options(
                                        fn () => collect([config()->string('auth.defaults.guard')])
                                            ->mapWithKeys(fn (string $guardName) => [$guardName => $guardName])
                                    )
                                    ->default(PermissionConfig::defaultGuardName())
                                    ->reactive(),
                            ]),
                    ]),
                Schemas\Components\Section::make(trans('Permissions'))
                    ->schema(fn (Get $get) => PermissionSchema::schema($get('guard_name'))),
            ])->columns(1);
    }

    /** @throws Exception */
    #[\Override]
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->translateLabel()
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => Str::headline($state))
                    ->color(Color::Blue)
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('guard_name')
                    ->translateLabel()
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('permissions_count')
                    ->translateLabel()
                    ->badge()
                    ->counts('permissions')
                    ->color(Color::Green)
                    ->sortable(),

                Tables\Columns\TextColumn::make('users_count')
                    ->translateLabel()
                    ->badge()
                    ->counts('users')
                    ->color(Color::Green)
                    ->sortable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->translateLabel()
                    ->sortable()
                    ->since()
                    ->dateTimeTooltip(),
            ])
            ->recordActions([
                Actions\ViewAction::make(),
                Actions\EditAction::make(),

                Actions\ActionGroup::make([
                    Actions\DeleteAction::make()
//                        ->disabled(fn (RoleContract $record): bool => $record->users->isNotEmpty())
                        ->tooltip(
                            fn (RoleContract $record) => $record->users->isNotEmpty()
                                ? trans('This role has users.')
                                : null
                        ),
                ]),
            ])
            ->defaultSort('updated_at', 'desc');
    }

    #[\Override]
    public static function getPages(): array
    {
        return [
            'index' => RoleResource\Pages\ListRoles::route('/'),
            'create' => RoleResource\Pages\CreateRole::route('/create'),
            'view' => RoleResource\Pages\ViewRole::route('/{record}'),
            'edit' => RoleResource\Pages\EditRole::route('/{record}/edit'),
        ];
    }

    /**
     * @return Builder<RoleContract&Model>
     */
    #[\Override]
    public static function getEloquentQuery(): Builder
    {
        /** @var Builder<RoleContract&Model> $query */
        $query = parent::getEloquentQuery();

        return $query->with('users');
    }
}
