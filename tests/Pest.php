<?php

declare(strict_types=1);

use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Database\Seeders\DefaultPermissionSeeder;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Database\Seeders\DefaultRoleSeeder;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Tests\Fixture\Models\User;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Tests\TestCase;

use function Pest\Laravel\seed;

uses(
    TestCase::class,
    LazilyRefreshDatabase::class
)
    ->beforeEach(function () {

        seed([
            DefaultRoleSeeder::class,
            DefaultPermissionSeeder::class,
        ]);

        Filament::setCurrentPanel(
            Filament::getPanel('test'),
        );

    })
    ->in(__DIR__);
