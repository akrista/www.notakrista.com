<?php

declare(strict_types=1);

use App\Filament\Resources\TransactionCategories\Pages\ListTransactionCategories;
use App\Models\TransactionCategory;
use Livewire\Livewire;

beforeEach(function (): void {
    TransactionCategory::query()->delete();
});

test('transaction categories list page loads for an admin', function (): void {
    $user = budgetAdmin(['view_any_transaction_category']);
    TransactionCategory::factory()->create();

    Livewire::actingAs($user)
        ->test(ListTransactionCategories::class)
        ->call('loadTable')
        ->assertCanSeeTableRecords(TransactionCategory::query()->get());
});

test('transaction category can be created via the resource', function (): void {
    $user = budgetAdmin(['view_any_transaction_category', 'create_transaction_category']);
    $category = TransactionCategory::factory()->create([
        'name' => 'Wellness Check',
        'slug' => 'wellness-check',
    ]);

    expect($category->fresh())
        ->name->toBe('Wellness Check')
        ->slug->toBe('wellness-check');
});
