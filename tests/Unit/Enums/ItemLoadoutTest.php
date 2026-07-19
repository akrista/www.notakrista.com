<?php

declare(strict_types=1);

use App\Enums\ItemLoadout;

test('has two loadouts with stable order', function (): void {
    expect(ItemLoadout::cases())
        ->toHaveCount(2)
        ->and(ItemLoadout::cases()[0])->toBe(ItemLoadout::Ranked)
        ->and(ItemLoadout::cases()[1])->toBe(ItemLoadout::Casual);
});

test('suffix maps ranked to pvp and casual to pve', function (): void {
    expect(ItemLoadout::Ranked->suffix())->toBe('PVP')
        ->and(ItemLoadout::Casual->suffix())->toBe('PVE');
});
