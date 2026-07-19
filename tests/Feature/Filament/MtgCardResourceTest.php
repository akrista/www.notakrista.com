<?php

declare(strict_types=1);

use App\Filament\Resources\MtgCards\Pages\ListMtgCards;
use App\Jobs\SyncMtgCardsJob;
use App\Models\MtgCard;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Livewire\Livewire;

beforeEach(function (): void {
    MtgCard::query()->forceDelete();
});

test('admin can render the mtg cards list page under a team tenant', function (): void {
    $user = User::factory()->create();
    $team = $user->personalTeam();

    setPermissionsTeamId($team->id);
    $role = Role::create(['name' => 'admin', 'guard_name' => 'web', 'team_id' => $team->id]);
    foreach (['view_any_mtg_card', 'view_mtg_card', 'create_mtg_card', 'update_mtg_card', 'delete_mtg_card'] as $perm) {
        $permission = Permission::query()->firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        $role->givePermissionTo($permission);
    }

    $user->assignRole($role);

    $this->actingAs($user);
    Filament::setCurrentPanel(Filament::getPanel('filament'));
    Filament::setTenant($team);

    // Fake the Scryfall API requests for card creation
    Http::fake([
        'api.scryfall.com/cards/*' => Http::response([
            'id' => fake()->uuid(),
            'name' => 'Test Card',
            'set' => 'aer',
            'collector_number' => '52',
            'type_line' => 'Artifact',
            'mana_cost' => '{1}',
            'rarity' => 'common',
            'prices' => [
                'usd' => '0.10',
            ],
            'image_uris' => [
                'normal' => 'https://cards.scryfall.io/normal/front/1/2/12.jpg',
            ],
        ], 200),
    ]);

    $cards = MtgCard::factory()->count(3)->create();

    Livewire::test(ListMtgCards::class)
        ->call('loadTable')
        ->assertOk()
        ->assertCanSeeTableRecords($cards);
});

test('console command mtg:sync-cards fetches data from scryfall and updates database', function (): void {
    Http::fake([
        'api.scryfall.com/cards/collection' => Http::response([
            'data' => [
                [
                    'id' => '56ebc372-aabd-4174-a943-c7bf59e5028d',
                    'name' => 'Solemn Simulacrum',
                    'set' => 'm21',
                    'collector_number' => '239',
                    'type_line' => 'Artifact Creature — Golem',
                    'mana_cost' => '{4}',
                    'rarity' => 'rare',
                    'prices' => [
                        'usd' => '0.45',
                    ],
                    'image_uris' => [
                        'normal' => 'https://cards.scryfall.io/normal/front/5/6/56ebc372-aabd-4174-a943-c7bf59e5028d.jpg',
                    ],
                ],
            ],
        ], 200),
        'api.scryfall.com/cards/m21/239' => Http::response([
            'id' => '56ebc372-aabd-4174-a943-c7bf59e5028d',
            'name' => 'Solemn Simulacrum',
            'set' => 'm21',
            'collector_number' => '239',
            'type_line' => 'Artifact Creature — Golem',
            'mana_cost' => '{4}',
            'rarity' => 'rare',
            'prices' => [
                'usd' => '0.45',
            ],
            'image_uris' => [
                'normal' => 'https://cards.scryfall.io/normal/front/5/6/56ebc372-aabd-4174-a943-c7bf59e5028d.jpg',
            ],
        ], 200),
    ]);

    $card = MtgCard::query()->create([
        'set' => 'm21',
        'number' => '239',
        'quantity' => 1,
    ]);

    // Reset details to check that console command updates them
    $card->name = null;
    $card->price = null;
    $card->saveQuietly();

    $this->artisan('mtg:sync-cards')
        ->assertSuccessful();

    $card->refresh();
    expect($card->name)->toBe('Solemn Simulacrum')
        ->and($card->type_line)->toBe('Artifact Creature — Golem')
        ->and((float) $card->price)->toBe(0.45)
        ->and($card->image_url)->toBe('https://cards.scryfall.io/normal/front/5/6/56ebc372-aabd-4174-a943-c7bf59e5028d.jpg');
});

test('model saving event automatically fetches scryfall details', function (): void {
    Http::fake([
        'api.scryfall.com/cards/aer/52' => Http::response([
            'id' => 'cb628e83-75b7-417c-959c-04358178cedd',
            'name' => 'Solemn Simulacrum',
            'set' => 'aer',
            'collector_number' => '52',
            'type_line' => 'Artifact',
            'mana_cost' => '{4}',
            'rarity' => 'rare',
            'prices' => [
                'usd' => '1.50',
            ],
            'image_uris' => [
                'normal' => 'https://cards.scryfall.io/normal/front/c/b/cb628e83.jpg',
            ],
        ], 200),
    ]);

    $card = MtgCard::query()->create([
        'set' => 'aer',
        'number' => '52',
        'quantity' => 2,
    ]);

    expect($card->name)->toBe('Solemn Simulacrum')
        ->and((float) $card->price)->toBe(1.50)
        ->and($card->scryfall_id)->toBe('cb628e83-75b7-417c-959c-04358178cedd');
});

test('admin can trigger mtg cards sync via background job from table toolbar', function (): void {
    Queue::fake();

    $user = User::factory()->create();
    $team = $user->personalTeam();

    setPermissionsTeamId($team->id);
    $role = Role::create(['name' => 'admin', 'guard_name' => 'web', 'team_id' => $team->id]);
    foreach (['view_any_mtg_card', 'view_mtg_card'] as $perm) {
        $permission = Permission::query()->firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        $role->givePermissionTo($permission);
    }

    $user->assignRole($role);

    $this->actingAs($user);
    Filament::setCurrentPanel(Filament::getPanel('filament'));
    Filament::setTenant($team);

    Livewire::test(ListMtgCards::class)
        ->callTableAction('sync_all')
        ->assertNotified('MTG Card Sync Queued');

    Queue::assertPushed(SyncMtgCardsJob::class);
});

test('admin can view and edit mtg card details and is_sold toggle on the form', function (): void {
    $user = User::factory()->create();
    $team = $user->personalTeam();

    setPermissionsTeamId($team->id);
    $role = Role::create(['name' => 'admin', 'guard_name' => 'web', 'team_id' => $team->id]);
    foreach (['view_any_mtg_card', 'view_mtg_card', 'create_mtg_card', 'update_mtg_card'] as $perm) {
        $permission = Permission::query()->firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        $role->givePermissionTo($permission);
    }

    $user->assignRole($role);

    $this->actingAs($user);
    Filament::setCurrentPanel(Filament::getPanel('filament'));
    Filament::setTenant($team);

    $this->withoutExceptionHandling();

    Http::fake();

    $card = MtgCard::factory()->create([
        'name' => 'Original Name',
        'is_sold' => false,
    ]);

    Livewire::test(ListMtgCards::class)
        ->call('loadTable')
        ->assertCanSeeTableRecords([$card])
        ->mountTableAction('edit', $card)
        ->set('mountedActions.0.data', [
            'set' => $card->set,
            'number' => $card->number,
            'quantity' => 5,
            'name' => 'Manually Edited Name',
            'is_sold' => true,
        ])
        ->callMountedTableAction()
        ->assertHasNoTableActionErrors();

    $card->refresh();
    expect($card->quantity)->toBe(5)
        ->and($card->name)->toBe('Manually Edited Name')
        ->and($card->is_sold)->toBeTrue();
});

test('admin can create mtg card via the header action', function (): void {
    $user = User::factory()->create();
    $team = $user->personalTeam();

    setPermissionsTeamId($team->id);
    $role = Role::create(['name' => 'admin', 'guard_name' => 'web', 'team_id' => $team->id]);
    foreach (['view_any_mtg_card', 'view_mtg_card', 'create_mtg_card'] as $perm) {
        $permission = Permission::query()->firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        $role->givePermissionTo($permission);
    }

    $user->assignRole($role);

    $this->actingAs($user);
    Filament::setCurrentPanel(Filament::getPanel('filament'));
    Filament::setTenant($team);

    Http::fake();

    Livewire::test(ListMtgCards::class)
        ->call('loadTable')
        ->mountAction('create')
        ->set('mountedActions.0.data', [
            'set' => 'm21',
            'number' => '239',
            'quantity' => 3,
        ])
        ->callMountedAction()
        ->assertHasNoActionErrors();

    expect(MtgCard::query()->where('set', 'm21')->where('number', '239')->first())
        ->not->toBeNull()
        ->and(MtgCard::query()->where('set', 'm21')->where('number', '239')->value('quantity'))->toBe(3);
});
