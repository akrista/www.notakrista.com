<?php

declare(strict_types=1);

use App\Filament\Resources\Translations\Pages\ListTranslations;
use App\Filament\Resources\Translations\TranslationResource;
use App\Models\LanguageLine;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Livewire\Livewire;

function translationAdmin(): User
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
        'view_any_language_line',
        'view_language_line',
        'create_language_line',
        'update_language_line',
        'delete_language_line',
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

beforeEach(function (): void {
    Cache::flush();
    app()->setLocale('en');
});

test('translation resource list page loads for an admin', function (): void {
    $user = translationAdmin();
    $line = LanguageLine::query()->create(['group' => 'a', 'key' => 'a', 'text' => ['en' => 'Account']]);

    Livewire::actingAs($user)
        ->test(ListTranslations::class)
        ->call('loadTable')
        ->assertCanSeeTableRecords([$line]);
});

test('translations can be created via the resource', function (): void {
    $user = translationAdmin();

    Livewire::actingAs($user)
        ->test(ListTranslations::class)
        ->callAction(CreateAction::class, [
            'group' => 'resources',
            'key' => 'item',
            'text' => [
                ['locale' => 'en', 'value' => 'Item'],
                ['locale' => 'es', 'value' => 'Objeto'],
            ],
        ])
        ->assertHasNoFormErrors();

    $line = LanguageLine::query()->where('group', 'resources')->where('key', 'item')->first();
    expect($line)->not->toBeNull()
        ->and($line->getTranslation('en'))->toBe('Item')
        ->and($line->getTranslation('es'))->toBe('Objeto');
});

test('translations can be edited via the resource and override the file value', function (): void {
    $user = translationAdmin();
    $line = LanguageLine::query()->create(['group' => 'resources', 'key' => 'user', 'text' => ['en' => 'Account']]);

    Livewire::actingAs($user)
        ->test(ListTranslations::class)
        ->callTableAction(EditAction::class, $line->getKey(), [
            'group' => 'resources',
            'key' => 'user',
            'text' => [
                ['locale' => 'en', 'value' => 'Member'],
                ['locale' => 'es', 'value' => 'Miembro'],
            ],
        ])
        ->assertHasNoFormErrors();

    $line->refresh();
    expect($line->getTranslation('en'))->toBe('Member')
        ->and($line->getTranslation('es'))->toBe('Miembro')
        ->and(__('resources.user'))->toBe('Member');
});

test('translations can be deleted and the file fallback is restored', function (): void {
    // The model-level `deleted` event is the contract; the Filament
    // `DeleteAction` on the resource just calls `delete()` on the record,
    // which fires the same event.
    $line = LanguageLine::query()->create(['group' => 'resources', 'key' => 'user', 'text' => ['en' => 'Account']]);

    expect(__('resources.user'))->toBe('Account');

    $line->delete();

    expect(LanguageLine::query()->where('key', 'user')->where('group', 'resources')->count())->toBe(0)
        ->and(__('resources.user'))->toBe('User');
});

test('the edit action renders for a record that exists', function (): void {
    $user = translationAdmin();
    $line = LanguageLine::query()->create(['group' => 'resources', 'key' => 'user', 'text' => ['en' => 'Account']]);

    Livewire::actingAs($user)
        ->test(ListTranslations::class)
        ->assertTableActionExists(EditAction::class);
});

test('the translation resource navigation group is settings', function (): void {
    expect(__('resources.translations'))->toBe('Translations')
        ->and(TranslationResource::getNavigationGroup())
        ->toBe(__('menu.nav_group.settings'));
});

test('the translation resource exposes the standard model labels and a non-tenant scope', function (): void {
    expect(TranslationResource::getModelLabel())->toBe(__('resources.translation'))
        ->and(TranslationResource::getPluralModelLabel())->toBe(__('resources.translations'))
        ->and(TranslationResource::isScopedToTenant())->toBeFalse();
});

test('the header sync action seeds new translations from the lang folder', function (): void {
    $user = translationAdmin();
    $base = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'sync-translations-livewire-' . uniqid('', true);

    File::makeDirectory($base . DIRECTORY_SEPARATOR . 'en', 0o755, true);
    File::put(
        $base . DIRECTORY_SEPARATOR . 'en' . DIRECTORY_SEPARATOR . 'resources.php',
        '<?php declare(strict_types=1); return ' . var_export(['user' => 'User'], true) . ';' . PHP_EOL,
    );

    // The header action always reads from lang_path(); redirect the helper
    // for the duration of this test so we don't touch the real lang/ folder.
    app()->useLangPath($base);

    try {
        Livewire::actingAs($user)
            ->test(ListTranslations::class)
            ->callAction('syncFromLangFiles')
            ->assertNotified();
    } finally {
        File::deleteDirectory($base);
    }

    $line = LanguageLine::query()->where('group', 'resources')->where('key', 'user')->first();
    expect($line)->not->toBeNull()
        ->and($line->getTranslation('en'))->toBe('User');
});

test('the header sync action preserves existing database values when the file matches', function (): void {
    $user = translationAdmin();
    $base = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'sync-translations-livewire-' . uniqid('', true);

    File::makeDirectory($base . DIRECTORY_SEPARATOR . 'en', 0o755, true);
    File::put(
        $base . DIRECTORY_SEPARATOR . 'en' . DIRECTORY_SEPARATOR . 'resources.php',
        '<?php declare(strict_types=1); return ' . var_export(['user' => 'User'], true) . ';' . PHP_EOL,
    );

    LanguageLine::query()->create([
        'group' => 'resources',
        'key' => 'user',
        'text' => ['en' => 'Account'],
    ]);

    app()->useLangPath($base);

    try {
        Livewire::actingAs($user)
            ->test(ListTranslations::class)
            ->callAction('syncFromLangFiles')
            ->assertNotified();
    } finally {
        File::deleteDirectory($base);
    }

    $line = LanguageLine::query()->where('group', 'resources')->where('key', 'user')->first();
    expect($line->getTranslation('en'))->toBe('Account');
});
