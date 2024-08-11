<?php

declare(strict_types=1);

namespace Lloricode\FilamentSpatieLaravelPermissionPlugin\Resources\RoleResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Actions\EditRoleAction;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Data\RoleData;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Resources\RoleResource;
use Spatie\Permission\Contracts\Role as RoleContract;

/**
 * @property-read RoleContract&Model $record
 */
class EditRole extends EditRecord
{
    protected static string $resource = RoleResource::class;

    #[\Override]
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    #[\Override]
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        /** @var RoleContract&Model $record */
        return app(EditRoleAction::class)
            ->execute($record, new RoleData(...$data));
    }
}
