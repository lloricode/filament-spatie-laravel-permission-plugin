<?php

declare(strict_types=1);

namespace Lloricode\FilamentSpatieLaravelPermissionPlugin\Database\Seeders\Support;

readonly class PermissionSeeder
{
    /**
     * @param  array<int, ResourceSeeder>  $resources
     */
    public function __construct(
        public array $panels,
        public array $pages,
        public array $widgets,
        public array $resources,
    ) {}

    /**
     * @return array<int, string>
     */
    public function allPermissionNames(): array
    {
        $collect = collect($this->panels)
            ->merge($this->pages)
            ->merge($this->widgets);

        foreach ($this->resources as $resource) {
            $collect = $collect->merge($resource->permissionNames);
        }

        return $collect->toArray();
    }
}
