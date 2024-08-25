<?php

declare(strict_types=1);

namespace Lloricode\FilamentSpatieLaravelPermissionPlugin\Resources\RoleResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Database\Eloquent\Model;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Resources\RoleResource;
use Spatie\Permission\Contracts\Role as RoleContract;

/**
 * @property-read RoleContract&Model $record
 */
class ViewRole extends ViewRecord
{
    protected static string $resource = RoleResource::class;

    #[\Override]
    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
