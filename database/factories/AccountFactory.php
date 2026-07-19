<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\AccountType;
use App\Models\Account;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Account>
 */
final class AccountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->company() . ' ' . fake()->randomElement(['Account', 'Wallet', 'Card']),
            'type' => AccountType::Bank,
            'currency' => 'USD',
            'opening_balance' => fake()->randomFloat(2, 0, 2_000_000),
            'icon' => '💳',
            'color_token' => 'blue',
            'donation_url' => null,
            'donation_address' => null,
            'donation_instructions' => null,
            'donation_qr_image' => null,
            'notes' => null,
            'is_active' => true,
            'position' => 0,
        ];
    }

    public function type(AccountType $type): static
    {
        return $this->state(fn (): array => ['type' => $type]);
    }

    public function inactive(): static
    {
        return $this->state(fn (): array => ['is_active' => false]);
    }

    public function donation(array $overrides = []): static
    {
        return $this->state(fn (): array => array_merge([
            'donation_url' => 'https://example.com/donate',
            'donation_address' => 'TEST-ADDRESS-1234',
            'donation_instructions' => 'Send only via the test network.',
            'donation_qr_image' => null,
            'donation_account_number' => '1234567890',
            'donation_aba' => '987654321',
            'donation_swift' => 'TESTSWIFT',
            'donation_id_cedula' => 'V-12345678',
        ], $overrides));
    }
}
