<?php

declare(strict_types=1);

use App\Actions\Transactions\CreateTransactionAction;
use App\Actions\Transactions\MarkTransactionPostedAction;
use App\Enums\TransactionDirection;
use App\Models\Account;
use App\Models\Transaction;
use App\Models\TransactionCategory;
use Illuminate\Support\Facades\Date;

beforeEach(function (): void {
    Transaction::query()->delete();
    TransactionCategory::query()->where('is_archived', true)->delete();
    Account::query()->delete();
});

test('create transaction action persists a simple outflow', function (): void {
    $account = Account::factory()->create();
    $action = resolve(CreateTransactionAction::class);

    $tx = $action->handle([
        'account_id' => $account->getKey(),
        'amount' => 150.50,
        'direction' => 'outflow',
        'occurred_on' => '2026-08-15',
        'payee_name' => 'Netflix',
        'memo' => 'Monthly subscription',
    ]);

    expect($tx->fresh())
        ->amount->toBe('150.50')
        ->direction->toBe(TransactionDirection::Outflow)
        ->memo->toBe('Monthly subscription')
        ->payee_name->toBe('Netflix')
        ->is_public->toBeFalse()
        ->posted_at->toBeNull();
});

test('create transaction action persists an inflow as public by default', function (): void {
    $account = Account::factory()->create();
    $tx = resolve(CreateTransactionAction::class)->handle([
        'account_id' => $account->getKey(),
        'amount' => 2000,
        'direction' => 'inflow',
        'occurred_on' => '2026-08-15',
        'is_public' => true,
    ]);

    expect($tx->fresh()->is_public)->toBeTrue();
});

test('create transaction action accepts a posted_at date', function (): void {
    $account = Account::factory()->create();
    $tx = resolve(CreateTransactionAction::class)->handle([
        'account_id' => $account->getKey(),
        'amount' => 9.99,
        'direction' => 'outflow',
        'occurred_on' => '2026-08-15',
        'posted_at' => '2026-08-15',
    ]);

    $fresh = $tx->fresh();
    expect($fresh->isPosted())->toBeTrue()
        ->and($fresh->posted_at->toDateString())->toBe('2026-08-15');
});

test('create transaction action can attach a category', function (): void {
    $account = Account::factory()->create();
    $category = TransactionCategory::factory()->create();
    $tx = resolve(CreateTransactionAction::class)->handle([
        'account_id' => $account->getKey(),
        'category_id' => $category->getKey(),
        'amount' => 30,
        'direction' => 'outflow',
        'occurred_on' => '2026-08-15',
    ]);

    expect($tx->fresh()->category_id)->toBe($category->getKey());
});

test('mark posted action sets posted_at when the transaction is not yet posted', function (): void {
    $account = Account::factory()->create();
    $transaction = Transaction::factory()->forAccount($account)->create();

    $ok = resolve(MarkTransactionPostedAction::class)->handle($transaction, Date::parse('2026-08-15'));

    expect($ok)->toBeTrue()
        ->and($transaction->fresh()->posted_at?->toDateString())->toBe('2026-08-15');
});

test('mark posted action is a no-op when the transaction is already posted', function (): void {
    $account = Account::factory()->create();
    $transaction = Transaction::factory()->forAccount($account)->posted()->create();
    $original = $transaction->fresh()->posted_at?->toDateString();

    $ok = resolve(MarkTransactionPostedAction::class)->handle($transaction, Date::parse('2030-01-01'));

    expect($ok)->toBeFalse()
        ->and($transaction->fresh()->posted_at?->toDateString())->toBe($original);
});

test('transaction mark unposted clears posted_at', function (): void {
    $account = Account::factory()->create();
    $transaction = Transaction::factory()->forAccount($account)->posted()->create();

    expect($transaction->fresh()->isPosted())->toBeTrue();

    $transaction->refresh();
    $transaction->markUnposted();

    expect($transaction->fresh()->isPosted())->toBeFalse();
});

test('transaction inflow scope filters to inflows only', function (): void {
    $account = Account::factory()->create();
    Transaction::factory()->forAccount($account)->inflow(100)->create();
    Transaction::factory()->forAccount($account)->outflow(50)->create();

    expect((float) Transaction::query()->inflows()->sum('amount'))->toBe(100.0);
});

test('transaction outflow scope filters to outflows only', function (): void {
    $account = Account::factory()->create();
    Transaction::factory()->forAccount($account)->inflow(100)->create();
    Transaction::factory()->forAccount($account)->outflow(50)->create();

    expect((float) Transaction::query()->outflows()->sum('amount'))->toBe(50.0);
});
