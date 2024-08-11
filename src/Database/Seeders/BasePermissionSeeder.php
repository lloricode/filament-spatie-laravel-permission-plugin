<?php

declare(strict_types=1);

namespace Lloricode\FilamentSpatieLaravelPermissionPlugin\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Spatie\Permission\Contracts\Permission as PermissionContract;

abstract class BasePermissionSeeder extends Seeder
{
    abstract protected function permissionsByGuard(): array;

    public function run(): void
    {
        $permissionClass = app(PermissionContract::class);

        collect($this->permissionsByGuard())
            ->map(fn (array $permissions) => collect($permissions))
            ->each(
                function (Collection $permissions, string $guard) use ($permissionClass) {
                    $this->command->getOutput()->info('Seeding permissions for guard: ' . $guard . ' ...');
                    $progressBar = $this->command->getOutput()->createProgressBar($permissions->count());
                    $progressBar->start();

                    $permissions->each(
                        function (string $permission) use ($permissionClass, $guard, $progressBar) {
                            $permissionClass::findOrCreate(name: $permission, guardName: $guard);
                            $progressBar->advance();
                        }
                    );

                    $progressBar->finish();
                    $this->command->getOutput()->info('Done Seeding permissions for guard: ' . $guard . '!');
                    $this->command->getOutput()->newLine();

                    $permissionClass::whereGuardName($guard)
                        ->whereNotIn('name', $permissions)
                        ->delete();
                }
            );
    }

    /** @param  class-string  $modelPolicy */
    protected static function generateFilamentResourcePermissions(string $modelPolicy): array
    {
        $reject = collect(['before', 'after']);

        foreach (class_uses_recursive($modelPolicy) as $trait) {
            $reject = $reject->merge(get_class_methods($trait));
        }

        $permissions = collect(get_class_methods($modelPolicy))
            ->reject(fn (string $functionName) => $reject->search($functionName) !== false)
            ->toArray();

        return self::generatePermissionGroup(
            (string) Str::of($modelPolicy)
                ->classBasename()
                ->replace('Policy', '')
                ->camel(),
            $permissions
        );
    }

    protected static function generatePermissionGroup(string $resourceName, array $permissions): array
    {
        return collect($permissions)
            ->map(fn (string $permission) => "{$resourceName}.{$permission}")
            ->prepend($resourceName)
            ->toArray();
    }
}
