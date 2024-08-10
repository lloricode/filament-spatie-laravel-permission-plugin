<?php

declare(strict_types=1);

namespace Lloricode\FilamentSpatieLaravelPermissionPlugin\Resources\RoleResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Actions\CreateRoleAction;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Data\RoleData;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Resources\RoleResource;

class CreateRole extends CreateRecord
{
    protected static string $resource = RoleResource::class;

    #[\Override]
    protected function handleRecordCreation(array $data): Model
    {
        return app(CreateRoleAction::class)
            ->execute(new RoleData(...$data));
    }
}
