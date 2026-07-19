<?php

declare(strict_types=1);

use App\Enums\TransactionDirection;
use App\Models\Account;
use App\Models\Schedule;
use App\Models\Transaction;

test('guest pages load successfully', function (string $url, string $textToSee): void {
    $response = $this->get($url);

    $response->assertStatus(200);
    $response->assertSee($textToSee);
})->with([
    ['/', 'Jorge Thomas'],
    ['/todoticket', 'Todoticket Calculator'],
    ['/character', 'Character Sheet'],
    ['/inventory', 'Inventory Bag'],
    ['/skills', 'Skills'],
    ['/stats', 'Achievements Unlocked'],
    ['/donations', 'Active Support Channels'],
    ['/foundry', 'The Foundry'],
]);

test('donations page lists donation-flagged accounts', function (): void {
    Account::factory()->donation()->create(['name' => 'PayPal Hero', 'currency' => 'USD']);
    Account::factory()->create(['name' => 'Empty Account', 'currency' => 'USD']);

    $response = $this->get(route('donations'));

    $response->assertOk()
        ->assertSee('PayPal Hero')
        ->assertDontSee('Empty Account');
});

test('donations page lists active support channels in the correct order', function (): void {
    Account::query()->delete();

    Account::factory()->donation()->create(['name' => 'PayPal', 'position' => 3]);
    Account::factory()->donation()->create(['name' => 'Binance', 'position' => 2]);
    Account::factory()->donation()->create(['name' => 'Facebank (Puerto Rico)', 'position' => 1]);
    Account::factory()->donation()->create(['name' => 'BDV', 'position' => 5]);
    Account::factory()->donation()->create(['name' => 'Bancamiga', 'position' => 4]);

    $response = $this->get(route('donations'));

    $response->assertOk();

    $donationAccounts = $response->viewData('donationAccounts');

    expect($donationAccounts->pluck('name')->toArray())->toBe([
        'Facebank (Puerto Rico)',
        'Binance',
        'PayPal',
        'Bancamiga',
        'BDV',
    ]);
});

test('donations page displays spanish translations when locale is set to es', function (): void {
    app()->setLocale('es');

    $response = $this->get(route('donations'));

    $response->assertOk()
        ->assertSee('Gratitud y Futuras Funciones')
        ->assertSee('Apoya mi Vida y Trabajo Open Source')
        ->assertDontSee('Gratitude & Future Features')
        ->assertDontSee('Support my Life & Open Source');
});

test('donations page targetGoal is dynamically computed from actual and forecasted public expenses', function (): void {
    $account = Account::factory()->create();

    // 1. Create a public transaction for this month (e.g. 50 USD)
    Transaction::factory()
        ->forAccount($account)
        ->outflow(50.00)
        ->public()
        ->onDate(now()->format('Y-m-05'))
        ->create();

    // 2. Create an active public schedule due this month (e.g. 120 USD)
    Schedule::factory()
        ->forAccount($account)
        ->create([
            'is_public' => true,
            'is_active' => true,
            'direction' => TransactionDirection::Outflow,
            'next_run_on' => now()->format('Y-m-15'),
            'amount' => 120.00,
        ]);

    // 3. Create a private schedule due this month (e.g. 300 USD) - should be ignored
    Schedule::factory()
        ->forAccount($account)
        ->create([
            'is_public' => false,
            'is_active' => true,
            'direction' => TransactionDirection::Outflow,
            'next_run_on' => now()->format('Y-m-15'),
            'amount' => 300.00,
        ]);

    $response = $this->get(route('donations'));

    $response->assertOk();

    // The target goal should be 50 (actual) + 120 (forecasted) = 170.00 USD
    $targetGoal = $response->viewData('targetGoal');
    expect($targetGoal)->toBe(170.00);
});
