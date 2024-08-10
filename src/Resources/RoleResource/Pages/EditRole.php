<?php

declare(strict_types=1);

namespace Lloricode\FilamentSpatieLaravelPermissionPlugin\Resources\RoleResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Actions\EditRoleAction;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Data\RoleData;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Models\Role;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Resources\RoleResource;

/**
 * @property-read Role $record
 */
class EditRole extends EditRecord
{
    protected static string $resource = RoleResource::class;

    #[\Override]
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->translateLabel(),
        ];
    }

    #[\Override]
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        /** @var Role $record */
        return app(EditRoleAction::class)
            ->execute($record, new RoleData(...$data));
    }
}
