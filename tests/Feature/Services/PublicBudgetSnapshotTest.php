<?php

declare(strict_types=1);

use App\Models\Account;
use App\Models\Transaction;
use App\Models\TransactionCategory;
use App\Services\Budget\MonthlySummaryService;
use App\Services\Budget\PublicBudgetSnapshot;
use Illuminate\Support\Collection;

beforeEach(function (): void {
    Transaction::query()->delete();
    Account::query()->delete();
    TransactionCategory::query()->where('is_archived', true)->delete();
});

test('public snapshot exposes the requested month label', function (): void {
    $snapshot = resolve(PublicBudgetSnapshot::class)->for('2026-08');

    expect($snapshot['year_month'])->toBe('2026-08')
        ->and($snapshot['display_currency'])->toBe('USD')
        ->and($snapshot['month_label'])->toContain('2026');
});

test('public snapshot reports zero totals when no public transactions exist', function (): void {
    $snapshot = resolve(PublicBudgetSnapshot::class)->for('2026-08');

    expect((float) $snapshot['totals']['income'])->toBe(0.0)
        ->and((float) $snapshot['totals']['spent'])->toBe(0.0)
        ->and((float) $snapshot['totals']['net'])->toBe(0.0);
});

test('public snapshot only includes transactions flagged is_public', function (): void {
    $account = Account::factory()->create();
    Transaction::factory()->forAccount($account)->inflow(1000)->public()->onDate('2026-08-05')->create();
    Transaction::factory()->forAccount($account)->inflow(5000)->onDate('2026-08-10')->create();
    Transaction::factory()->forAccount($account)->outflow(200)->public()->onDate('2026-08-12')->create();
    Transaction::factory()->forAccount($account)->outflow(800)->onDate('2026-08-15')->create();

    $snapshot = resolve(PublicBudgetSnapshot::class)->for('2026-08');

    expect((float) $snapshot['totals']['income'])->toBe(1000.0)
        ->and((float) $snapshot['totals']['spent'])->toBe(200.0)
        ->and((float) $snapshot['totals']['net'])->toBe(800.0);
});

test('public snapshot includes category breakdown for public outflows', function (): void {
    $account = Account::factory()->create();
    $category = TransactionCategory::factory()->create();
    Transaction::factory()->forAccount($account)->forCategory($category)->outflow(50)->public()->onDate('2026-08-05')->create();
    Transaction::factory()->forAccount($account)->forCategory($category)->outflow(75)->public()->onDate('2026-08-15')->create();

    $snapshot = resolve(PublicBudgetSnapshot::class)->for('2026-08');
    $cats = $snapshot['categories'];

    expect($cats)->toBeInstanceOf(Collection::class)
        ->and($cats->first()['name'])->toBe($category->name)
        ->and((float) $cats->first()['spent'])->toBe(125.0);
});

test('public snapshot previous months array has 3 entries', function (): void {
    $snapshot = resolve(PublicBudgetSnapshot::class)->for('2026-08');

    expect($snapshot['previous_months'])->toHaveCount(3);
});

test('public snapshot includes donation accounts that have any donation field set', function (): void {
    Account::factory()->donation()->create(['name' => 'PayPal Account']);
    Account::factory()->create(['name' => 'Empty Account']);

    $snapshot = resolve(PublicBudgetSnapshot::class)->for('2026-08');

    expect($snapshot['donation_accounts']->pluck('name')->all())
        ->toContain('PayPal Account')
        ->not->toContain('Empty Account');
});
test('monthly summary service returns admin view with all transactions', function (): void {
    $account = Account::factory()->create();
    Transaction::factory()->forAccount($account)->inflow(1000)->onDate('2026-08-05')->create();
    Transaction::factory()->forAccount($account)->outflow(300)->onDate('2026-08-10')->create();

    $summary = resolve(MonthlySummaryService::class)->for('2026-08');

    expect((float) $summary['income'])->toBe(1000.0)
        ->and((float) $summary['spent'])->toBe(300.0)
        ->and((float) $summary['net'])->toBe(700.0)
        ->and($summary['transaction_count'])->toBe(2)
        ->and($summary['display_currency'] ?? $summary['currency'] ?? 'USD')->toBe('USD');
});
