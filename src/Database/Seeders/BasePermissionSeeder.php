<?php

declare(strict_types=1);

namespace Lloricode\FilamentSpatieLaravelPermissionPlugin\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Database\Seeders\Support\PermissionSeeder;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Database\Seeders\Support\ResourceSeeder;
use Spatie\Permission\Contracts\Permission as PermissionContract;
use Spatie\Permission\Contracts\Role as RoleContract;

abstract class BasePermissionSeeder extends Seeder
{
    public function __construct(
        protected readonly RoleContract $roleContract,
        protected readonly PermissionContract $permissionContract,
    ) {}

    /**
     * @return array<string, PermissionSeeder>
     */
    abstract protected function permissionsByGuard(): array;

    public function run(): void
    {

        collect($this->permissionsByGuard())
            ->each(
                function (PermissionSeeder $permissionSeeder, string $guardName) {
                    $output = $this->command->getOutput();

                    $output->title(sprintf('Seeding permissions for guard: [%s] ...', $guardName));

                    $output->text('panels');
                    $this->seedPanelsPagesWidgets($permissionSeeder->panels, $guardName);

                    $output->text('pages');
                    $this->seedPanelsPagesWidgets($permissionSeeder->pages, $guardName);

                    $output->text('widgets');
                    $this->seedPanelsPagesWidgets($permissionSeeder->widgets, $guardName);

                    $output->text('resources');
                    $this->seedResource($permissionSeeder->resources, $guardName);

                    $allPermissionNames = $permissionSeeder->allPermissionNames();

                    $output->info(sprintf(
                        'Done Seeding permissions for guard: [%s] with total of [%d] permissions.!',
                        $guardName,
                        count($allPermissionNames)
                    ));

                    $permissionClass::whereGuardName($guardName)
                        ->whereNotIn('name', $allPermissionNames)
                        ->delete();

                    $this->tablePagesPanelWidgets('panels', $permissionSeeder->panels);
                    $this->tablePagesPanelWidgets('pages', $permissionSeeder->pages);
                    $this->tablePagesPanelWidgets('widgets', $permissionSeeder->widgets);
                    $this->tableResources($permissionSeeder->resources);

                    $output->newLine();
                }
            );

    }

    /**
     * @param  array<int, string>  $permissions
     */
    protected function tablePagesPanelWidgets(string $type, array $permissions): void
    {
        $output = $this->command->getOutput();

        $rows = [];
        foreach ($permissions as $permissionName) {
            $rows[] = [$permissionName];
        }
        $output->table([$type], $rows);

    }

    /**
     * @param  array<int, ResourceSeeder>  $resourceSeeders
     */
    protected function tableResources(array $resourceSeeders): void
    {
        $output = $this->command->getOutput();

        $rows = [];
        foreach ($resourceSeeders as $resourceSeeder) {
            $rows[] = [
                Str::of($resourceSeeder->resource)->classBasename(),
                Str::of($resourceSeeder->model)->classBasename(),
                Str::of($resourceSeeder->modelPolicy)->classBasename(),
                implode(PHP_EOL, $resourceSeeder->permissionNames),
            ];
        }

        $output->table(['resource', 'model', 'modelPolicy', 'permissionNames'], $rows);
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
            ->sort()
            ->toArray();
    }

    /**
     * @param  array<int, string>  $permissionNames
     */
    protected function seedPanelsPagesWidgets(array $permissionNames, string $guardName): void
    {

        $permissionNames = collect($permissionNames);

        $output = $this->command->getOutput();
        $output->progressStart($permissionNames->count());

        $permissionNames->sort()->each(
            function (string $permission) use ($guardName, $output) {
                $this->permissionContract->findOrCreate(name: $permission, guardName: $guardName);
                $output->progressAdvance();
            }
        );

        $output->progressFinish();
    }

    /**
     * @param  array<int, ResourceSeeder>  $resourcePermissionNames
     */
    protected function seedResource(array $resourcePermissionNames, string $guardName): void
    {

        $permissionNames = collect();

        foreach ($resourcePermissionNames as $resourcePermissionName) {
            $permissionNames = $permissionNames->merge($resourcePermissionName->permissionNames);
        }

        $output = $this->command->getOutput();
        $output->progressStart($permissionNames->count());

        $permissionNames->sort()->each(
            function (string $permission) use ($guardName, $output) {
                $this->permissionContract->findOrCreate(name: $permission, guardName: $guardName);
                $output->progressAdvance();
            }
        );

        $output->progressFinish();
    }
}
