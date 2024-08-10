<?php

namespace Lloricode\FilamentSpatieLaravelPermissionPlugin\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Lloricode\FilamentSpatieLaravelPermissionPlugin\FilamentSpatieLaravelPermissionPlugin
 */
class FilamentSpatieLaravelPermissionPlugin extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Lloricode\FilamentSpatieLaravelPermissionPlugin\FilamentSpatieLaravelPermissionPlugin::class;
    }
}
