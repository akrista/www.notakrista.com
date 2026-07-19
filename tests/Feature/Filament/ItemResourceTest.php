<?php

declare(strict_types=1);

use App\Filament\Resources\Items\ItemResource;
use App\Filament\Resources\Items\Pages\ListItems;
use App\Models\Category;
use App\Models\Item;
use App\Models\LanguageLine;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Filament\Facades\Filament;
use Livewire\Livewire;

test('admin can render the items list page under a team tenant', function (): void {
    $user = User::factory()->create();
    $team = $user->personalTeam();

    setPermissionsTeamId($team->id);
    $role = Role::create(['name' => 'admin', 'guard_name' => 'web', 'team_id' => $team->id]);
    foreach (['view_any_item', 'view_item', 'create_item', 'update_item', 'delete_item'] as $perm) {
        $permission = Permission::query()->firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        $role->givePermissionTo($permission);
    }

    $user->assignRole($role);

    $this->actingAs($user);
    Filament::setCurrentPanel(Filament::getPanel('filament'));
    Filament::setTenant($team);

    $items = Item::factory()->count(3)->create();

    Livewire::test(ListItems::class)
        ->call('loadTable')
        ->assertOk()
        ->assertCanSeeTableRecords($items);
});

test('ItemResource::handleRecordCreation writes the translations to language_lines', function (): void {
    $category = Category::query()->where('slug', 'tech')->firstOrFail();

    $item = ItemResource::handleRecordCreation([
        'slug' => 'new-thing',
        'rarity' => 'common',
        'icon' => '📦',
        'category_id' => $category->getKey(),
        'position' => 0,
        'stats' => [],
        'loadout' => null,
        'equipment_slot' => null,
        'image_url' => null,
        'acquired_at' => null,
        'name_en' => 'New Thing',
        'name_es' => 'Cosa Nueva',
        'type_en' => 'Misc',
        'type_es' => 'Varios',
        'desc_en' => 'A new thing.',
        'desc_es' => 'Una cosa nueva.',
    ]);

    expect($item->slug)->toBe('new-thing');

    foreach (['name', 'type', 'desc'] as $field) {
        $line = LanguageLine::query()
            ->where('group', LanguageLine::ITEMS_GROUP)
            ->where('key', sprintf('new-thing.%s', $field))
            ->first();

        expect($line)->not->toBeNull();
    }

    expect($item->name('es'))->toBe('Cosa Nueva')
        ->and($item->typeLabel('es'))->toBe('Varios')
        ->and($item->description('es'))->toBe('Una cosa nueva.');
});
