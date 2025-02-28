<?php

declare(strict_types=1);

use Lloricode\FilamentSpatieLaravelPermissionPlugin\Commands\PermissionSyncCommand;

use function Pest\Laravel\artisan;

it('run seed command', function () {
    artisan(PermissionSyncCommand::class)
        ->assertSuccessful();
});
