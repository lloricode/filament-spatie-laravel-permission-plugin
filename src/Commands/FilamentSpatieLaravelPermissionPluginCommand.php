<?php

namespace Lloricode\FilamentSpatieLaravelPermissionPlugin\Commands;

use Illuminate\Console\Command;

class FilamentSpatieLaravelPermissionPluginCommand extends Command
{
    public $signature = 'filament-spatie-laravel-permission-plugin';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
