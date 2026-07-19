<?php

declare(strict_types=1);

use App\Enums\AccountType;
use App\Models\Account;

it('seeds a default cash account with expected fields', function (): void {
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
