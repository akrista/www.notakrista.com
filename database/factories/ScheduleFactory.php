<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\BillCadence;
use App\Enums\TransactionDirection;
use App\Models\Account;
use App\Models\Schedule;
use App\Models\TransactionCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Schedule>
 */
final class ScheduleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),
            'account_id' => Account::factory(),
            'category_id' => null,
            'payee_name' => fake()->company(),
            'memo' => null,
            'amount' => fake()->randomFloat(2, 1_000, 50_000),
            'cadence' => BillCadence::Monthly,
            'direction' => TransactionDirection::Outflow,
            'next_run_on' => now()->toDateString(),
            'last_run_on' => null,
            'auto_post' => true,
            'is_public' => false,
            'is_active' => true,
        ];
    }

    public function forAccount(Account $account): static
    {
        return $this->state(fn (): array => ['account_id' => $account->getKey()]);
    }

    public function forCategory(TransactionCategory $category): static
    {
        return $this->state(fn (): array => ['category_id' => $category->getKey()]);
    }

    public function cadence(BillCadence $cadence): static
    {
        return $this->state(fn (): array => ['cadence' => $cadence]);
    }

    public function dueOn(string $date): static
    {
        return $this->state(fn (): array => ['next_run_on' => $date]);
    }

    public function inactive(): static
    {
        return $this->state(fn (): array => ['is_active' => false]);
    }

    public function manual(): static
    {
        return $this->state(fn (): array => ['auto_post' => false]);
    }
}
