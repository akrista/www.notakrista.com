<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;
use Spatie\Permission\Models\Permission;

/**
 * @extends Factory<Role>
 */
final class RoleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->jobTitle(),
            'guard_name' => 'web',
        ];
    }

    /**
     * Attach a random permission to the role.
     */
    public function withPermission(): static
    {
        return $this->state(fn (array $attributes): array => [
            'permissions' => Permission::query()->inRandomOrder()->first(),
        ]);
    }
}
