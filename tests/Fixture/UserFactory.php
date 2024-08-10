<?php

declare(strict_types=1);

namespace Lloricode\FilamentSpatieLaravelPermissionPlugin\Tests\Fixture;

use Illuminate\Database\Eloquent\Factories\Factory;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Tests\Fixture\Models\User;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'email' => fake()->unique()->safeEmail(),
        ];
    }
}
