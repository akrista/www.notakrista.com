<?php

declare(strict_types=1);

use App\Filament\Resources\YugiohCards\Pages\ListYugiohCards;
use App\Jobs\SyncYugiohCardsJob;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Models\YugiohCard;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

beforeEach(function (): void {
    YugiohCard::query()->forceDelete();
    Storage::fake('public');
});

test('admin can render the yugioh cards list page under a team tenant', function (): void {
    $user = User::factory()->create();
    $team = $user->personalTeam();

    setPermissionsTeamId($team->id);
    $role = Role::create(['name' => 'admin', 'guard_name' => 'web', 'team_id' => $team->id]);
    foreach (['view_any_yugioh_card', 'view_yugioh_card', 'create_yugioh_card', 'update_yugioh_card', 'delete_yugioh_card'] as $perm) {
        $permission = Permission::query()->firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        $role->givePermissionTo($permission);
    }

    $user->assignRole($role);

    $this->actingAs($user);
    Filament::setCurrentPanel(Filament::getPanel('filament'));
    Filament::setTenant($team);

    // Fake the YGOPRODeck API requests for card creation/sync
    Http::fake([
        'db.ygoprodeck.com/api/v7/cardsetsinfo.php*' => Http::response([
            'id' => 35537860,
            'name' => 'Synchro Blast Wave',
            'set_name' => "Starter Deck: Yu-Gi-Oh! 5D's",
            'set_code' => '5DS1-EN021',
            'set_rarity' => 'Common',
            'set_price' => '1.13',
        ], 200),
        'db.ygoprodeck.com/api/v7/cardinfo.php*' => Http::response([
            'data' => [
                [
                    'id' => 35537860,
                    'name' => 'Synchro Blast Wave',
                    'type' => 'Spell Card',
                    'humanReadableCardType' => 'Normal Spell',
                    'frameType' => 'spell',
                    'desc' => 'If you control a Synchro Monster...',
                    'race' => 'Normal',
                    'ygoprodeck_url' => 'https://ygoprodeck.com/card/synchro-blast-wave-3010',
                    'card_images' => [
                        [
                            'id' => 35537860,
                            'image_url' => 'https://images.ygoprodeck.com/images/cards/35537860.jpg',
                        ],
                    ],
                    'card_prices' => [
                        [
                            'tcgplayer_price' => '0.85',
                            'cardmarket_price' => '0.12',
                        ],
                    ],
                ],
            ],
        ], 200),
        'images.ygoprodeck.com/*' => Http::response('fake-image-data', 200),
    ]);

    $cards = YugiohCard::factory()->count(3)->create();

    Livewire::test(ListYugiohCards::class)
        ->call('loadTable')
        ->assertOk()
        ->assertCanSeeTableRecords($cards);
});

test('console command yugioh:sync-cards fetches data from ygoprodeck and updates database', function (): void {
    Http::fake([
        'db.ygoprodeck.com/api/v7/cardsetsinfo.php*' => Http::response([
            'id' => 35537860,
            'name' => 'Synchro Blast Wave',
            'set_name' => "Starter Deck: Yu-Gi-Oh! 5D's",
            'set_code' => '5DS1-EN021',
            'set_rarity' => 'Common',
            'set_price' => '1.13',
        ], 200),
        'db.ygoprodeck.com/api/v7/cardinfo.php*' => Http::response([
            'data' => [
                [
                    'id' => 35537860,
                    'name' => 'Synchro Blast Wave',
                    'type' => 'Spell Card',
                    'humanReadableCardType' => 'Normal Spell',
                    'frameType' => 'spell',
                    'desc' => 'If you control a Synchro Monster...',
                    'race' => 'Normal',
                    'ygoprodeck_url' => 'https://ygoprodeck.com/card/synchro-blast-wave-3010',
                    'card_images' => [
                        [
                            'id' => 35537860,
                            'image_url' => 'https://images.ygoprodeck.com/images/cards/35537860.jpg',
                        ],
                    ],
                    'card_prices' => [
                        [
                            'tcgplayer_price' => '0.85',
                            'cardmarket_price' => '0.12',
                        ],
                    ],
                ],
            ],
        ], 200),
        'images.ygoprodeck.com/*' => Http::response('fake-image-data', 200),
    ]);

    $card = YugiohCard::query()->create([
        'setcode' => '5DS1-EN021',
        'quantity' => 1,
    ]);

    // Reset details to check that console command updates them
    $card->name = null;
    $card->price = null;
    $card->saveQuietly();

    $this->artisan('yugioh:sync-cards')
        ->assertSuccessful();

    $card->refresh();
    expect($card->name)->toBe('Synchro Blast Wave')
        ->and($card->type)->toBe('Spell Card')
        ->and((float) $card->price)->toBe(1.13)
        ->and((float) $card->card_price)->toBe(0.85)
        ->and($card->image_url)->toContain('yugioh/cards/35537860.jpg');

    Storage::disk('public')->assertExists('yugioh/cards/35537860.jpg');
});

test('model saving event automatically fetches ygoprodeck details', function (): void {
    Http::fake([
        'db.ygoprodeck.com/api/v7/cardsetsinfo.php*' => Http::response([
            'id' => 35537860,
            'name' => 'Synchro Blast Wave',
            'set_name' => "Starter Deck: Yu-Gi-Oh! 5D's",
            'set_code' => '5DS1-EN021',
            'set_rarity' => 'Common',
            'set_price' => '1.13',
        ], 200),
        'db.ygoprodeck.com/api/v7/cardinfo.php*' => Http::response([
            'data' => [
                [
                    'id' => 35537860,
                    'name' => 'Synchro Blast Wave',
                    'type' => 'Spell Card',
                    'humanReadableCardType' => 'Normal Spell',
                    'frameType' => 'spell',
                    'desc' => 'If you control a Synchro Monster...',
                    'race' => 'Normal',
                    'ygoprodeck_url' => 'https://ygoprodeck.com/card/synchro-blast-wave-3010',
                    'card_images' => [
                        [
                            'id' => 35537860,
                            'image_url' => 'https://images.ygoprodeck.com/images/cards/35537860.jpg',
                        ],
                    ],
                    'card_prices' => [
                        [
                            'tcgplayer_price' => '0.85',
                            'cardmarket_price' => '0.12',
                        ],
                    ],
                ],
            ],
        ], 200),
        'images.ygoprodeck.com/*' => Http::response('fake-image-data', 200),
    ]);

    $card = YugiohCard::query()->create([
        'setcode' => '5DS1-EN021',
        'quantity' => 2,
    ]);

    expect($card->name)->toBe('Synchro Blast Wave')
        ->and((float) $card->price)->toBe(1.13)
        ->and((float) $card->card_price)->toBe(0.85)
        ->and($card->ygoprodeck_id)->toBe(35537860)
        ->and($card->image_url)->toContain('yugioh/cards/35537860.jpg');

    Storage::disk('public')->assertExists('yugioh/cards/35537860.jpg');
});

test('admin can trigger yugioh cards sync via background job from table toolbar', function (): void {
    Queue::fake();
    Http::fake([
        'db.ygoprodeck.com/api/v7/cardsetsinfo.php*' => Http::response([
            'id' => 35537860,
            'name' => 'Synchro Blast Wave',
            'set_name' => "Starter Deck: Yu-Gi-Oh! 5D's",
            'set_code' => '5DS1-EN021',
            'set_rarity' => 'Common',
            'set_price' => '1.13',
        ], 200),
        'db.ygoprodeck.com/api/v7/cardinfo.php*' => Http::response([
            'data' => [
                [
                    'id' => 35537860,
                    'name' => 'Synchro Blast Wave',
                    'type' => 'Spell Card',
                    'humanReadableCardType' => 'Normal Spell',
                    'frameType' => 'spell',
                    'desc' => 'If you control a Synchro Monster...',
                    'race' => 'Normal',
                    'ygoprodeck_url' => 'https://ygoprodeck.com/card/synchro-blast-wave-3010',
                    'card_images' => [
                        [
                            'id' => 35537860,
                            'image_url' => 'https://images.ygoprodeck.com/images/cards/35537860.jpg',
                        ],
                    ],
                    'card_prices' => [
                        [
                            'tcgplayer_price' => '0.85',
                            'cardmarket_price' => '0.12',
                        ],
                    ],
                ],
            ],
        ], 200),
        'images.ygoprodeck.com/*' => Http::response('fake-image-data', 200),
    ]);

    $user = User::factory()->create();
    $team = $user->personalTeam();

    setPermissionsTeamId($team->id);
    $role = Role::create(['name' => 'admin', 'guard_name' => 'web', 'team_id' => $team->id]);
    foreach (['view_any_yugioh_card', 'view_yugioh_card'] as $perm) {
        $permission = Permission::query()->firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        $role->givePermissionTo($permission);
    }

    $user->assignRole($role);

    $cards = YugiohCard::factory()->count(3)->create();
    $cardIds = $cards->pluck('id')->toArray();

    $this->actingAs($user);
    Filament::setCurrentPanel(Filament::getPanel('filament'));
    Filament::setTenant($team);

    Livewire::test(ListYugiohCards::class)
        ->callTableAction('sync_all')
        ->assertNotified('Yu-Gi-Oh Card Sync Queued');

    Queue::assertPushed(SyncYugiohCardsJob::class, fn(SyncYugiohCardsJob $job): bool => array_diff($job->cardIds, $cardIds) === [] && array_diff($cardIds, $job->cardIds) === []);
});

test('admin can view and edit yugioh card details and is_sold toggle on the form', function (): void {
    $user = User::factory()->create();
    $team = $user->personalTeam();

    setPermissionsTeamId($team->id);
    $role = Role::create(['name' => 'admin', 'guard_name' => 'web', 'team_id' => $team->id]);
    foreach (['view_any_yugioh_card', 'view_yugioh_card', 'create_yugioh_card', 'update_yugioh_card'] as $perm) {
        $permission = Permission::query()->firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        $role->givePermissionTo($permission);
    }

    $user->assignRole($role);

    $this->actingAs($user);
    Filament::setCurrentPanel(Filament::getPanel('filament'));
    Filament::setTenant($team);

    $card = YugiohCard::factory()->create([
        'name' => 'Original YGO Name',
        'is_sold' => false,
    ]);

    Http::fake();

    Livewire::test(ListYugiohCards::class)
        ->call('loadTable')
        ->assertCanSeeTableRecords([$card])
        ->mountTableAction('edit', $card)
        ->set('mountedActions.0.data', [
            'setcode' => $card->setcode,
            'quantity' => 5,
            'name' => 'Manually Edited YGO Name',
            'is_sold' => true,
        ])
        ->callMountedTableAction()
        ->assertHasNoTableActionErrors();

    $card->refresh();
    expect($card->quantity)->toBe(5)
        ->and($card->name)->toBe('Manually Edited YGO Name')
        ->and($card->is_sold)->toBeTrue();
});

test('admin can create yugioh card via the header action', function (): void {
    $user = User::factory()->create();
    $team = $user->personalTeam();

    setPermissionsTeamId($team->id);
    $role = Role::create(['name' => 'admin', 'guard_name' => 'web', 'team_id' => $team->id]);
    foreach (['view_any_yugioh_card', 'view_yugioh_card', 'create_yugioh_card'] as $perm) {
        $permission = Permission::query()->firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        $role->givePermissionTo($permission);
    }

    $user->assignRole($role);

    $this->actingAs($user);
    Filament::setCurrentPanel(Filament::getPanel('filament'));
    Filament::setTenant($team);

    Http::fake();

    Livewire::test(ListYugiohCards::class)
        ->call('loadTable')
        ->mountAction('create')
        ->set('mountedActions.0.data', [
            'setcode' => '5DS1-EN021',
            'quantity' => 3,
        ])
        ->callMountedAction()
        ->assertHasNoActionErrors();

    expect(YugiohCard::query()->where('setcode', '5DS1-EN021')->first())
        ->not->toBeNull()
        ->and(YugiohCard::query()->where('setcode', '5DS1-EN021')->value('quantity'))->toBe(3);
});
