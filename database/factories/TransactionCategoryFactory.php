<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\TransactionCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TransactionCategory>
 */
final class TransactionCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'slug' => fake()->unique()->slug(2),
            'name' => fake()->words(2, true),
            'icon' => '🏷️',
            'color_token' => 'muted',
            'position' => 0,
            'is_archived' => false,
        ];
    }

    public function archived(): static
    {
        return $this->state(fn (): array => ['is_archived' => true]);
    }
}
