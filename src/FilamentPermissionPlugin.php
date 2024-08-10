<?php

declare(strict_types=1);

namespace Lloricode\FilamentSpatieLaravelPermissionPlugin;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Resources\RoleResource;

class FilamentPermissionPlugin implements Plugin
{
    /** @var class-string */
    private string $defaultResource = RoleResource::class;

    public function getId(): string
    {
        return 'filament-spatie-laravel-permission-plugin';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([$this->defaultResource]);
    }

    public function boot(Panel $panel): void
    {
        //
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
    }

    /**
     * @param  class-string  $resource
     */
    public function usingResource(string $resource): static
    {
        $this->defaultResource = $resource;

        return $this;
    }
}
