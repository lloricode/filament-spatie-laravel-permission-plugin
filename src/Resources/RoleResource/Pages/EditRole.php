<?php

declare(strict_types=1);

namespace Lloricode\FilamentSpatieLaravelPermissionPlugin\Resources\RoleResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Config\PermissionConfig;
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
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    #[\Override]
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        /** @var RoleContract&Model $record */
        $roleNames = PermissionConfig::roleNamesByGuardName($data['guard_name'] ?? null);

        $isExtraRole = in_array($record->name, $roleNames, true);

        if ($record->name !== $data['name']) {

            if ($isExtraRole) {
                abort(400, trans('Cannot update role name of this role.'));
            }

        }

        if (! $isExtraRole) {
            $record->update([
                'name' => $data['name'],
                'guard_name' => $data['guard_name'],
            ]);
        }

        $record->syncPermissions($data['permissions']);

        return $record;
    }
}
