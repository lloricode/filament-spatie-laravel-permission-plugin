<?php

declare(strict_types=1);

namespace Lloricode\FilamentSpatieLaravelPermissionPlugin\Database\Seeders\Support;

readonly class PermissionSeeder
{
    /**
     * @param  array<int, ResourceSeeder>  $resources
     * @param  array<int, string>  $panels
     * @param  array<int, string>  $pages
     * @param  array<int, string>  $widgets
     * @param  array<int, string>  $customs
     */
    public function __construct(
        public array $resources,
        public array $panels,
        public array $pages,
        public array $widgets,
        public array $customs,
    ) {}

    /**
     * @return array<int, string>
     */
    public function allPermissionNames(): array
    {
        $collect = collect($this->panels)
            ->merge($this->pages)
            ->merge($this->widgets)
            ->merge($this->customs);

        foreach ($this->resources as $resource) {
            $collect = $collect->merge($resource->permissionNames);
        }

        return $collect->toArray();
    }
}
