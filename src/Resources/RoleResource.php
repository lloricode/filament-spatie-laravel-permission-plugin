<?php

declare(strict_types=1);

namespace Lloricode\FilamentSpatieLaravelPermissionPlugin\Resources;

use Exception;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Config\PermissionConfig;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Resources\RoleResource\Schema\PermissionSchema;
use Spatie\Permission\Contracts\Role as RoleContract;
use Spatie\Permission\PermissionRegistrar;

class RoleResource extends Resource
{
    #[\Override]
    public static function getModel(): string
    {
        return app(PermissionRegistrar::class)
            ->getRoleClass();
    }

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'name';

    #[\Override]
    public static function getNavigationGroup(): ?string
    {
        return trans('Access');
    }

    #[\Override]
    public static function form(Form $form): Form
    {
        $guards = collect([config('auth.defaults.guard')]);

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

        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Grid::make()
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
                                    ->in($guards)
                                    ->options(
                                        $guards
                                            ->mapWithKeys(fn (string $guardName) => [$guardName => $guardName])
                                    )
                                    ->default(PermissionConfig::defaultGuardName())
                                    ->reactive(),
                            ]),
                    ]),
                Forms\Components\Section::make(trans('Permissions'))
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
                    ->colors(['primary'])
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
                    ->colors(['success'])
                    ->sortable(),

                Tables\Columns\TextColumn::make('users_count')
                    ->translateLabel()
                    ->badge()
                    ->counts('users')
                    ->colors(['success'])
                    ->sortable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->translateLabel()
                    ->sortable()
                    ->since()
                    ->dateTimeTooltip(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),

                Tables\Actions\ActionGroup::make([
                    Tables\Actions\DeleteAction::make()
//                        ->disabled(fn (RoleContract $record): bool => $record->users->isNotEmpty())
                        ->tooltip(
                            fn (RoleContract $record): ?string => $record->users->isNotEmpty()
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
     * @return Builder<RoleContract>
     */
    public static function getEloquentQuery(): Builder
    {
        /** @var Builder<RoleContract> $query */
        $query = parent::getEloquentQuery();

        return $query->with('users');
    }
}
