<?php

declare(strict_types=1);

namespace Lloricode\FilamentSpatieLaravelPermissionPlugin\Resources\RoleResource\Pages;

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
}
