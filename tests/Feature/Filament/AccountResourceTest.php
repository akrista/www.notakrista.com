<?php

declare(strict_types=1);

use App\Enums\AccountType;
use App\Filament\Resources\Accounts\Pages\ListAccounts;
use App\Models\Account;
use Livewire\Livewire;

beforeEach(function (): void {
    Account::query()->forceDelete();
});

test('accounts list page loads for an admin', function (): void {
    $user = budgetAdmin(['view_any_account']);
    Account::factory()->create();

    Livewire::actingAs($user)
        ->test(ListAccounts::class)
        ->call('loadTable')
        ->assertCanSeeTableRecords(Account::query()->get());
});

test('account can be created via the resource', function (): void {
    $user = budgetAdmin(['view_any_account', 'create_account']);
    $account = Account::factory()->create([
        'name' => 'Binance',
        'type' => AccountType::Exchange,
        'currency' => 'USDT',
        'donation_url' => 'https://example.com/donate',
        'donation_address' => 'TEST-ADDRESS',
    ]);

    expect($account->currency)->toBe('USDT')
        ->and($account->hasDonationInfo())->toBeTrue();
});

test('account has_donation_info returns true when any donation field is set', function (): void {
    $withUrl = Account::factory()->donation(['donation_url' => 'https://x.test'])->create();
    $withAddress = Account::factory()->donation(['donation_url' => null, 'donation_address' => 'ABC'])->create();
    $empty = Account::factory()->create([
        'donation_url' => null,
        'donation_address' => null,
        'donation_qr_image' => null,
    ]);

    expect($withUrl->hasDonationInfo())->toBeTrue()
        ->and($withAddress->hasDonationInfo())->toBeTrue()
        ->and($empty->hasDonationInfo())->toBeFalse();
});

test('database seeds a default cash account with expected fields', function (): void {
    $migration = require database_path('migrations/2026_07_17_100600_seed_default_accounts_with_donation_info.php');
    $migration->up();

    $cash = Account::query()->where('name', 'Cash')->first();

    expect($cash)->not->toBeNull()
        ->and($cash->type)->toBe(AccountType::Cash)
        ->and($cash->currency)->toBe('USD')
        ->and($cash->icon)->toBe('💵')
        ->and($cash->color_token)->toBe('accent')
        ->and($cash->position)->toBe(7)
        ->and($cash->donation_url)->toBeNull()
        ->and($cash->donation_address)->toBeNull()
        ->and($cash->donation_instructions)->toBeNull()
        ->and($cash->donation_qr_image)->toBeNull();
});
