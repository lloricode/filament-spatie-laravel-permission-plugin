<?php

declare(strict_types=1);

namespace Lloricode\FilamentSpatieLaravelPermissionPlugin\Resources\RoleResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Resources\RoleResource;
use Spatie\Permission\Contracts\Role as RoleContract;

class CreateRole extends CreateRecord
{
    protected static string $resource = RoleResource::class;

    #[\Override]
    protected function handleRecordCreation(array $data): Model
    {
        /** @var RoleContract&Model $role */
        $role = app(RoleContract::class)::findOrCreate(
            name: $data['name'],
            guardName: $data['guard_name'],
        );

        $role->givePermissionTo($data['permissions']);

        return $role;
    }
}
