<?php

declare(strict_types=1);

use App\Models\User;
use App\Models\WarframeAccount;
use App\Models\WarframeItem;
use App\Models\WarframeUserItem;
use App\Services\AlecaFrameImportService;
use App\Services\WfcdCatalogSyncService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('wfcd catalog sync service creates warframe items', function (): void {
    $syncService = new WfcdCatalogSyncService();

    $mockItems = [
        [
            'uniqueName' => '/Lotus/Powersuits/Excalibur/Excalibur',
            'name' => 'Excalibur',
            'category' => 'Warframes',
            'type' => 'Warframe',
            'description' => 'A master of gun and blade.',
            'imageName' => 'excalibur.png',
        ],
        [
            'uniqueName' => '/Lotus/Weapons/Tenno/Rifle/HeavyRifle',
            'name' => 'Gorgon',
            'category' => 'Primary',
            'type' => 'Rifle',
            'description' => 'Heavy assault rifle.',
            'imageName' => 'gorgon.png',
        ],
    ];

    $count = $syncService->sync($mockItems);

    expect($count)->toBe(2);

    $this->assertDatabaseHas('warframe_items', [
        'unique_name' => '/Lotus/Powersuits/Excalibur/Excalibur',
        'name' => 'Excalibur',
    ]);
});

test('alecaframe import service parses complete payload, economy, relics, rivens, and cosmetics', function (): void {
    $user = User::factory()->create();

    // Create static catalog item beforehand
    WarframeItem::query()->create([
        'unique_name' => '/Lotus/Powersuits/Excalibur/Excalibur',
        'name' => 'Excalibur',
        'category' => 'Warframes',
        'type' => 'Warframe',
        'description' => 'A master of gun and blade.',
        'image_name' => 'excalibur.png',
    ]);

    $payload = [
        'ActiveAvatarImageType' => '/Lotus/Types/StoreItems/AvatarImages/ImageExcaliburPrimeDark',
        'RegularCredits' => 15500200,
        'PremiumCredits' => 4200,
        'VoidTraces' => 1250,
        'Endo' => 85000,
        'MasteryRank' => 30,
        'Boosters' => [
            ['ItemType' => '/Lotus/Types/Boosters/AffinityBooster', 'ExpiryDate' => 1784046140],
        ],
        'Suits' => [
            [
                'ItemType' => '/Lotus/Powersuits/Excalibur/Excalibur',
                'XP' => 300000,
                'Features' => 3,
            ],
        ],
        'LongGuns' => [
            [
                'ItemType' => '/Lotus/Weapons/Tenno/Rifle/HeavyRifle',
                'XP' => 300000,
                'Features' => 5,
            ],
        ],
        'Upgrades' => [
            [
                'ItemType' => '/Lotus/Upgrades/Mods/Rifle/WeaponDamageAmountMod',
                'ItemCount' => 2,
                'Rank' => 10,
                'FusionLimit' => 10,
            ],
        ],
        'RivenMods' => [
            [
                'ItemType' => '/Lotus/Upgrades/Mods/Randomized/LotusRivenMod',
                'ItemName' => 'Gorgon Crita-ata',
                'Rerolls' => 12,
                'MasteryReq' => 14,
            ],
        ],
        'LevelKeys' => [
            [
                'ItemType' => '/Lotus/Types/Game/Projections/LithA1Projection',
                'ItemCount' => 15,
                'Rank' => 3, // Radiant
            ],
        ],
    ];

    $key = "LEO-ALEC\tEO-ALEC";
    $iv = "12FGB36-LE3-q=9\0";
    $encrypted = openssl_encrypt(json_encode($payload), 'aes-128-cbc', $key, OPENSSL_RAW_DATA, $iv);

    $importService = new AlecaFrameImportService();
    $account = $importService->import($encrypted, $user);

    expect($account)->toBeInstanceOf(WarframeAccount::class)
        ->and($account->mastery_rank)->toBe(30)
        ->and($account->credits)->toBe(15500200)
        ->and($account->platinum)->toBe(4200)
        ->and($account->void_traces)->toBe(1250)
        ->and($account->endo)->toBe(85000)
        ->and($account->total_warframes)->toBe(1)
        ->and($account->total_weapons)->toBe(1)
        ->and($account->total_relics)->toBe(15);

    $this->assertDatabaseHas('warframe_user_items', [
        'warframe_account_id' => $account->id,
        'item_type' => '/Lotus/Powersuits/Excalibur/Excalibur',
        'category' => 'Warframe',
        'level' => 30,
        'formas' => 3,
    ]);

    $relic = WarframeUserItem::query()->where('category', 'Relic')->first();
    expect($relic)->not->toBeNull()
        ->and($relic->refinement)->toBe('Radiant')
        ->and($relic->item_count)->toBe(15);

    $mod = WarframeUserItem::query()->where('category', 'Mod')->first();
    expect($mod)->not->toBeNull()
        ->and($mod->fusion_rank)->toBe(10)
        ->and($mod->item_count)->toBe(2);

    $riven = WarframeUserItem::query()->where('category', 'Riven')->first();
    expect($riven)->not->toBeNull()
        ->and($riven->riven_stats['rerolls'])->toBe(12);
});
