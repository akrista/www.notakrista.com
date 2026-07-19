<?php

declare(strict_types=1);

use App\Enums\TransactionDirection;
use App\Filament\Resources\Transactions\Pages\ListTransactions;
use App\Models\Account;
use App\Models\Transaction;
use App\Models\TransactionCategory;
use Filament\Actions\Testing\TestAction;
use Livewire\Livewire;

beforeEach(function (): void {
    Transaction::query()->delete();
    TransactionCategory::query()->where('is_archived', true)->delete();
    Account::query()->delete();
});

test('transactions list page loads for an admin', function (): void {
    $user = budgetAdmin(['view_any_transaction']);
    $account = Account::factory()->create();
    Transaction::factory()->forAccount($account)->create();

    Livewire::actingAs($user)
        ->test(ListTransactions::class)
        ->call('loadTable')
        ->assertCanSeeTableRecords(Transaction::query()->get());
});

test('transaction can be created via the resource', function (): void {
    $user = budgetAdmin(['view_any_transaction', 'create_transaction']);
    $account = Account::factory()->create();
    $category = TransactionCategory::factory()->create();

    $transaction = Transaction::query()->create([
        'account_id' => $account->getKey(),
        'category_id' => $category->getKey(),
        'payee_name' => 'Netflix',
        'amount' => 9.99,
        'direction' => TransactionDirection::Outflow->value,
        'occurred_on' => '2026-08-15',
        'is_public' => true,
    ]);

    expect($transaction)->not->toBeNull()
        ->and((float) $transaction->amount)->toBe(9.99)
        ->and($transaction->is_public)->toBeTrue();
});

test('transaction mark posted action stamps posted_at', function (): void {
    $user = budgetAdmin(['view_any_transaction', 'update_transaction']);
    $account = Account::factory()->create();
    $transaction = Transaction::factory()->forAccount($account)->create();

    Livewire::actingAs($user)
        ->test(ListTransactions::class)
        ->callAction(TestAction::make('markPosted')->table($transaction));

    expect($transaction->fresh()->isPosted())->toBeTrue();
});

test('transaction mark unposted action clears posted_at', function (): void {
    $user = budgetAdmin(['view_any_transaction', 'update_transaction']);
    $account = Account::factory()->create();
    $transaction = Transaction::factory()->forAccount($account)->posted()->create();

    Livewire::actingAs($user)
        ->test(ListTransactions::class)
        ->callAction(TestAction::make('markUnposted')->table($transaction));

    expect($transaction->fresh()->isPosted())->toBeFalse();
});
