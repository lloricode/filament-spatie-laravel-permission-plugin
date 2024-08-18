<?php

declare(strict_types=1);

namespace Lloricode\FilamentSpatieLaravelPermissionPlugin\Data;

final readonly class RoleData
{
    /** @param  array<int, string>  $permissions */
    public function __construct(
        public string $name,
        public array $permissions,
        public ?string $guard_name = null,
    ) {}
}
