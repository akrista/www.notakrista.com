<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\YugiohCard;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<YugiohCard>
 */
final class YugiohCardFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'setcode' => mb_strtoupper(fake()->bothify('????-EN###')),
            'quantity' => fake()->numberBetween(1, 10),
            'is_sold' => false,
            'name' => fake()->words(2, true),
            'type' => 'Effect Monster',
            'frame_type' => 'effect',
            'rarity' => fake()->randomElement(['Common', 'Rare', 'Super Rare', 'Ultra Rare', 'Secret Rare']),
            'price' => fake()->randomFloat(2, 0.10, 50.00),
            'image_url' => fake()->imageUrl(),
            'ygoprodeck_id' => fake()->numberBetween(10000000, 99999999),
        ];
    }
}
