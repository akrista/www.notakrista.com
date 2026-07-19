<?php

declare(strict_types=1);

use App\Actions\Schedules\PostScheduleAction;
use App\Enums\BillCadence;
use App\Enums\TransactionDirection;
use App\Models\Account;
use App\Models\Schedule;
use App\Models\Transaction;
use App\Models\TransactionCategory;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;

beforeEach(function (): void {
    Transaction::query()->forceDelete();
    Schedule::query()->forceDelete();
    TransactionCategory::query()->where('is_archived', true)->forceDelete();
    Account::query()->forceDelete();
});

test('post schedule action creates a posted transaction and advances next run', function (): void {
    $account = Account::factory()->create();
    $category = TransactionCategory::factory()->create();
    $schedule = Schedule::factory()
        ->forAccount($account)
        ->forCategory($category)
        ->cadence(BillCadence::Monthly)
        ->dueOn('2026-08-15')
        ->create([
            'amount' => 150,
            'payee_name' => 'Netflix',
        ]);

    $action = resolve(PostScheduleAction::class);
    $transaction = $action->handle($schedule, Date::parse('2026-08-15'));

    expect($transaction)->not->toBeNull()
        ->and((float) $transaction->amount)->toBe(150.0)
        ->and($transaction->occurred_on->toDateString())->toBe('2026-08-15')
        ->and($transaction->payee_name)->toBe('Netflix')
        ->and($transaction->posted_at?->toDateString())->toBe('2026-08-15');

    $schedule->refresh();
    expect($schedule->last_run_on?->toDateString())->toBe('2026-08-15')
        ->and($schedule->next_run_on->toDateString())->toBe('2026-09-15');
});

test('post schedule action creates a pending transaction when auto_post is false', function (): void {
    $account = Account::factory()->create();
    $schedule = Schedule::factory()
        ->forAccount($account)
        ->cadence(BillCadence::Monthly)
        ->dueOn('2026-08-15')
        ->manual()
        ->create();

    $transaction = resolve(PostScheduleAction::class)->handle($schedule, Date::parse('2026-08-15'));

    expect($transaction->posted_at)->toBeNull()
        ->and($transaction->isPosted())->toBeFalse();
});

test('post schedule action skips inactive schedules', function (): void {
    $account = Account::factory()->create();
    $schedule = Schedule::factory()
        ->forAccount($account)
        ->inactive()
        ->create();

    $action = resolve(PostScheduleAction::class);
    $result = $action->handle($schedule, Date::now());

    expect($result)->toBeNull();
});

test('post schedule action writes an inflow transaction for an inflow schedule', function (): void {
    $account = Account::factory()->create();
    $schedule = Schedule::factory()
        ->forAccount($account)
        ->cadence(BillCadence::Monthly)
        ->dueOn('2026-08-15')
        ->create([
            'direction' => TransactionDirection::Inflow,
            'amount' => 1500,
        ]);

    $transaction = resolve(PostScheduleAction::class)->handle($schedule, Date::parse('2026-08-15'));

    expect($transaction->direction)->toBe(TransactionDirection::Inflow);
});

test('post schedule advances next run by cadence interval for biweekly', function (): void {
    $account = Account::factory()->create();
    $schedule = Schedule::factory()
        ->forAccount($account)
        ->cadence(BillCadence::Biweekly)
        ->dueOn('2026-08-01')
        ->create();

    $action = resolve(PostScheduleAction::class);
    $action->handle($schedule, Date::parse('2026-08-01'));

    $schedule->refresh();
    expect($schedule->next_run_on->toDateString())->toBe('2026-08-15');
});

test('schedule is_due returns true when next run is on or before today', function (): void {
    $schedule = Schedule::factory()->dueOn('2026-08-01')->create();
    $schedule->refresh();

    expect($schedule->next_run_on?->toDateString())->toBe('2026-08-01')
        ->and($schedule->isDue(Date::parse('2026-08-15')))->toBeTrue()
        ->and($schedule->isDue(Date::parse('2026-08-01')))->toBeTrue()
        ->and($schedule->isDue(Date::parse('2026-07-25')))->toBeFalse();
});

test('due on or before scope filters past-due schedules', function (): void {
    $past = Schedule::factory()->dueOn('2026-08-01')->create();
    $future = Schedule::factory()->dueOn('2030-01-01')->create();
    $today = Schedule::factory()->dueOn('2026-08-15')->create();

    $raw = DB::table('schedules')
        ->pluck('next_run_on', 'id')
        ->all();

    test()->expect(array_keys($raw))->toHaveCount(3);
    test()->expect(mb_substr((string) $raw[$past->getKey()], 0, 10))->toBe('2026-08-01');

    $due = Schedule::query()
        ->dueOnOrBefore('2026-08-15')
        ->pluck('id')
        ->all();

    test()->expect($due)->toContain($past->getKey());
    test()->expect($due)->toContain($today->getKey());
    test()->expect($due)->not->toContain($future->getKey());
});

test('post schedule action propagates is_public to the created transaction', function (): void {
    $account = Account::factory()->create();
    $schedule = Schedule::factory()
        ->forAccount($account)
        ->create([
            'is_public' => true,
        ]);

    $transaction = resolve(PostScheduleAction::class)->handle($schedule, Date::parse('2026-08-15'));

    expect($transaction->is_public)->toBeTrue();
});
