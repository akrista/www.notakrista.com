<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\TransactionDirection;
use App\Models\Account;
use App\Models\Transaction;
use App\Models\TransactionCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Transaction>
 */
final class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'account_id' => Account::factory(),
            'category_id' => null,
            'memo' => null,
            'amount' => fake()->randomFloat(2, 1_000, 50_000),
            'direction' => TransactionDirection::Outflow,
            'occurred_on' => fake()->dateTimeBetween('-30 days', 'now')->format('Y-m-d'),
            'posted_at' => null,
            'is_public' => false,
            'payee_name' => fake()->company(),
        ];
    }

    public function inflow(?float $amount = null): static
    {
        return $this->state(fn (): array => [
            'direction' => TransactionDirection::Inflow,
            'amount' => $amount ?? fake()->randomFloat(2, 50_000, 500_000),
        ]);
    }

    public function outflow(?float $amount = null): static
    {
        return $this->state(fn (): array => [
            'direction' => TransactionDirection::Outflow,
            'amount' => $amount ?? fake()->randomFloat(2, 1_000, 50_000),
        ]);
    }

    public function forAccount(Account $account): static
    {
        return $this->state(fn (): array => ['account_id' => $account->getKey()]);
    }

    public function forCategory(TransactionCategory $category): static
    {
        return $this->state(fn (): array => ['category_id' => $category->getKey()]);
    }

    public function onDate(string $date): static
    {
        return $this->state(fn (): array => ['occurred_on' => $date]);
    }

    public function inMonth(string $yearMonth): static
    {
        $start = strtotime($yearMonth . '-01');
        $end = strtotime($yearMonth . '-01 +1 month -1 day');

        return $this->state(fn (): array => [
            'occurred_on' => date('Y-m-d', fake()->dateTimeBetween('@' . $start, '@' . $end)->getTimestamp()),
        ]);
    }

    public function posted(): static
    {
        return $this->state(fn (): array => [
            'posted_at' => now()->toDateString(),
        ]);
    }

    public function public(): static
    {
        return $this->state(fn (): array => ['is_public' => true]);
    }
}
