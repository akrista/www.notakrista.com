<?php

declare(strict_types=1);

use App\Enums\ItemRarity;

test('has four tiers with ascending order', function (): void {
    $cases = ItemRarity::cases();

    expect($cases)->toHaveCount(4)
        ->and($cases[0])->toBe(ItemRarity::Common)
        ->and($cases[1])->toBe(ItemRarity::Rare)
        ->and($cases[2])->toBe(ItemRarity::Epic)
        ->and($cases[3])->toBe(ItemRarity::Legendary);
});

test('color tokens match the inventory view css variables', function (string $case, string $token): void {
    expect(ItemRarity::from($case)->colorToken())->toBe($token);
})->with([
    ['common', 'muted'],
    ['rare', 'blue'],
    ['epic', 'accent'],
    ['legendary', 'yellow'],
]);

test('tier increases with rarity', function (): void {
    expect(ItemRarity::Common->tier())
        ->toBeLessThan(ItemRarity::Rare->tier())
        ->toBeLessThan(ItemRarity::Epic->tier())
        ->toBeLessThan(ItemRarity::Legendary->tier());
});
