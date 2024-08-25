<?php

declare(strict_types=1);

namespace Lloricode\FilamentSpatieLaravelPermissionPlugin\Data;

use Lloricode\FilamentSpatieLaravelPermissionPlugin\Enums\PermissionType;

final readonly class PermissionData
{
    public bool $is_parent;

    public PermissionType $type;

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

        $this->type = match ($this->parent_name) {
            PermissionType::panels->value => PermissionType::panels,
            PermissionType::pages->value => PermissionType::pages,
            PermissionType::widgets->value => PermissionType::widgets,
            PermissionType::customs->value => PermissionType::customs,
            default => PermissionType::resources,
        };

    }
}
