<?php

declare(strict_types=1);

namespace Lloricode\FilamentSpatieLaravelPermissionPlugin\Resources\RoleResource\Support;

final readonly class PermissionData
{
    public bool $is_parent;

    public string $parent_name;

    public ?string $child_name;

    public function __construct(
        public string $name,
    ) {

        $exploded = explode('.', $name);

        if (count($exploded) === 1) {

            $this->is_parent = true;
            $this->parent_name = $this->name;
            $this->child_name = null;
        } else {
            $this->is_parent = false;
            $this->parent_name = $exploded[0];
            $this->child_name = $exploded[1];
        }

    }
}
