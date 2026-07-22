<?php

declare(strict_types=1);

use App\Filament\Resources\WarframeAccounts\Pages\ListWarframeAccounts;
use App\Services\AlecaFrameParser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('alecaframe parser decrypts aes encrypted payload correctly', function (): void {
    $payload = [
        'ActiveAvatarImageType' => '/Lotus/Types/StoreItems/AvatarImages/ImageExcaliburPrimeDark',
        'MasteryRank' => 30,
        'Boosters' => [
            ['ItemType' => '/Lotus/Types/Boosters/AffinityBooster', 'ExpiryDate' => 1784046140],
        ],
    ];

    $json = json_encode($payload);
    $key = "LEO-ALEC\tEO-ALEC";
    $iv = "12FGB36-LE3-q=9\0";
    $encrypted = openssl_encrypt($json, 'aes-128-cbc', $key, OPENSSL_RAW_DATA, $iv);

    $parsed = AlecaFrameParser::parse($encrypted);

    expect($parsed)->toBeArray()
        ->and($parsed['MasteryRank'])->toBe(30)
        ->and($parsed['ActiveAvatarImageType'])->toContain('ExcaliburPrimeDark');
});

test('alecaframe parser merges outer payload with inner inventory json and extracts mastery rank', function (): void {
    $innerPayload = [
        'ActiveAvatarImageType' => '/Lotus/Types/StoreItems/AvatarImages/ImageVoltPrime',
        'Suits' => [],
    ];

    $outerPayload = [
        'PlayerRank' => 28,
        'RegularCredits' => 5000000,
        'InventoryJson' => json_encode($innerPayload),
    ];

    $json = json_encode($outerPayload);
    $key = "LEO-ALEC\tEO-ALEC";
    $iv = "12FGB36-LE3-q=9\0";
    $encrypted = openssl_encrypt($json, 'aes-128-cbc', $key, OPENSSL_RAW_DATA, $iv);

    $parsed = AlecaFrameParser::parse($encrypted);

    expect($parsed)->toBeArray()
        ->and($parsed['PlayerRank'])->toBe(28)
        ->and($parsed['RegularCredits'])->toBe(5000000)
        ->and($parsed['ActiveAvatarImageType'])->toContain('VoltPrime');
});

test('filament import action parses file and creates warframe account record', function (): void {
    $user = budgetAdmin([
        'view_any_warframe_account',
        'view_warframe_account',
        'create_warframe_account',
        'update_warframe_account',
        'delete_warframe_account',
    ]);

    $payload = [
        'ActiveAvatarImageType' => '/Lotus/Types/StoreItems/AvatarImages/ImageMagPrime',
        'MasteryRank' => 25,
        'Boosters' => [],
    ];

    $key = "LEO-ALEC\tEO-ALEC";
    $iv = "12FGB36-LE3-q=9\0";
    $encrypted = openssl_encrypt(json_encode($payload), 'aes-128-cbc', $key, OPENSSL_RAW_DATA, $iv);

    $file = UploadedFile::fake()->createWithContent('lastData.dat', $encrypted);

    Livewire::actingAs($user)
        ->test(ListWarframeAccounts::class)
        ->call('loadTable')
        ->callAction('importAlecaFrame', [
            'file' => $file,
        ])
        ->assertHasNoActionErrors();

    $this->assertDatabaseHas('warframe_accounts', [
        'user_id' => $user->id,
        'mastery_rank' => 25,
        'active_avatar' => '/Lotus/Types/StoreItems/AvatarImages/ImageMagPrime',
    ]);
});
