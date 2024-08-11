<?php

declare(strict_types=1);

namespace Lloricode\FilamentSpatieLaravelPermissionPlugin\Database\Seeders\Support;

readonly class ResourceSeeder
{
    /**
     * @param  array<int, string>  $permissionNames
     */
    public function __construct(
        public string $resource,
        public string $model,
        public string $modelPolicy,
        public array $permissionNames
    ) {}
}
