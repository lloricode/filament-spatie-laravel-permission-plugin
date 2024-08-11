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
                function (Collection $permissions, string $guardName) use ($permissionClass) {
                    $output = $this->command->getOutput();

                    $output->info('Seeding permissions for guard: ' . $guardName . ' ...');

                    $output->progressStart($permissions->count());

                    $permissions->sort()->each(
                        function (string $permission) use ($permissionClass, $guardName, $output) {
                            $permissionClass->findOrCreate(name: $permission, guardName: $guardName);
                            $output->progressAdvance();
                        }
                    );

                    $output->progressFinish();

                    $output->info('Done Seeding permissions for guard: ' . $guardName . '!');
                    $output->newLine();

                    $permissionClass::whereGuardName($guardName)
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
