<?php

declare(strict_types=1);

use App\Filament\Resources\Locales\LocaleResource;
use App\Filament\Resources\Locales\Pages\ListLocales;
use App\Models\Locale;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Facades\Filament;
use Livewire\Livewire;

function localeAdmin(): User
{
    $user = User::factory()->create();
    $team = $user->personalTeam();

    setPermissionsTeamId($team->id);
    $role = Role::query()->firstOrCreate([
        'name' => 'admin',
        'guard_name' => 'web',
        'team_id' => $team->id,
    ]);

    $perms = collect([
        'view_any_locale',
        'view_locale',
        'create_locale',
        'update_locale',
        'delete_locale',
    ])->map(
        fn (string $name): Permission => Permission::query()->firstOrCreate([
            'name' => $name,
            'guard_name' => 'web',
        ]),
    );

    $role->givePermissionTo($perms->all());
    $user->assignRole($role);

    test()->actingAs($user);

    Filament::setCurrentPanel(Filament::getPanel('filament'));
    Filament::setTenant($team);

    return $user;
}

test('locale resource list page loads for an admin', function (): void {
    $user = localeAdmin();
    $locales = Locale::factory()->count(3)->create();

    Livewire::actingAs($user)
        ->test(ListLocales::class)
        ->call('loadTable')
        ->assertCanSeeTableRecords($locales);
});

test('locale can be created via the resource', function (): void {
    $user = localeAdmin();

    Livewire::actingAs($user)
        ->test(ListLocales::class)
        ->callAction(CreateAction::class, [
            'code' => 'fr',
            'name' => 'French',
            'native_name' => 'Français',
            'direction' => 'ltr',
            'is_active' => true,
            'is_default' => false,
            'position' => 3,
        ])
        ->assertHasNoFormErrors();

    expect(Locale::query()->where('code', 'fr')->first())
        ->not->toBeNull()
        ->and(Locale::query()->where('code', 'fr')->value('native_name'))->toBe('Français');
});

test('locale can be edited via the resource', function (): void {
    $user = localeAdmin();
    $locale = Locale::factory()->create(['code' => 'fr', 'name' => 'French']);

    Livewire::actingAs($user)
        ->test(ListLocales::class)
        ->callTableAction(EditAction::class, $locale->getKey(), [
            'code' => 'fr',
            'name' => 'French (France)',
            'native_name' => 'Français',
            'direction' => 'ltr',
            'is_active' => true,
            'is_default' => false,
            'position' => 0,
        ])
        ->assertHasNoFormErrors();

    expect($locale->fresh()->name)->toBe('French (France)');
});

test('toggling is_default to true clears the flag on every other locale', function (): void {
    $user = localeAdmin();
    $english = Locale::query()->where('code', 'en')->firstOrFail();
    $spanish = Locale::query()->where('code', 'es')->firstOrFail();

    expect($english->is_default)->toBeTrue();

    Livewire::actingAs($user)
        ->test(ListLocales::class)
        ->callTableAction(EditAction::class, $spanish->getKey(), [
            'code' => 'es',
            'name' => 'Spanish',
            'native_name' => 'Español',
            'direction' => 'ltr',
            'is_active' => true,
            'is_default' => true,
            'position' => $spanish->position,
        ])
        ->assertHasNoFormErrors();

    expect($spanish->fresh()->is_default)->toBeTrue()
        ->and($english->fresh()->is_default)->toBeFalse();
});

test('the locale resource navigation group is settings and labels are translated', function (): void {
    expect(LocaleResource::getNavigationGroup())->toBe(__('menu.nav_group.settings'))
        ->and(LocaleResource::getModelLabel())->toBe(__('resources.locale'))
        ->and(LocaleResource::getPluralModelLabel())->toBe(__('resources.locales'))
        ->and(LocaleResource::isScopedToTenant())->toBeFalse();
});
