<?php

declare(strict_types=1);

use App\Enums\BillCadence;
use App\Enums\TransactionDirection;
use App\Filament\Resources\Schedules\Pages\ListSchedules;
use App\Models\Account;
use App\Models\Schedule;
use App\Models\Transaction;
use App\Models\TransactionCategory;
use Livewire\Livewire;

beforeEach(function (): void {
    Transaction::query()->delete();
    Schedule::query()->delete();
    Account::query()->delete();
});

test('schedules list page loads for an admin', function (): void {
    $user = budgetAdmin(['view_any_schedule']);
    $account = Account::factory()->create();
    Schedule::factory()->forAccount($account)->create();

    Livewire::actingAs($user)
        ->test(ListSchedules::class)
        ->call('loadTable')
        ->assertCanSeeTableRecords(Schedule::query()->get());
});

test('schedule can be created via the resource', function (): void {
    $account = Account::factory()->create();
    $category = TransactionCategory::factory()->create();

    $schedule = Schedule::query()->create([
        'name' => 'Monthly Netflix',
        'account_id' => $account->getKey(),
        'category_id' => $category->getKey(),
        'payee_name' => 'Netflix',
        'amount' => 9.99,
        'cadence' => BillCadence::Monthly->value,
        'direction' => TransactionDirection::Outflow->value,
        'next_run_on' => '2026-08-15',
        'auto_post' => true,
        'is_active' => true,
    ]);

    expect($schedule)->not->toBeNull()
        ->and($schedule->cadence)->toBe(BillCadence::Monthly)
        ->and((float) $schedule->amount)->toBe(9.99);
});
