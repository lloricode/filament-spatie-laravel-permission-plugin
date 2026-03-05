<?php

declare(strict_types=1);

namespace Lloricode\FilamentSpatieLaravelPermissionPlugin\Database\Seeders\Support;

readonly class PermissionSeeder
{
    /**
     * @param  list<ResourceSeeder>  $resources
     * @param  list<non-empty-string>  $panels
     * @param  list<non-empty-string>  $pages
     * @param  list<non-empty-string>  $widgets
     * @param  list<non-empty-string>  $customs
     */
    public function __construct(
        public array $resources,
        public array $panels,
        public array $pages,
        public array $widgets,
        public array $customs,
    ) {}

    /**
     * @return list<non-empty-string>
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

        /** @phpstan-ignore return.type */
        return $collect->toArray();
    }
}
