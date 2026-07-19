<?php

declare(strict_types=1);

use App\Filament\Resources\HomePhrases\Pages\ListHomePhrases;
use App\Models\LanguageLine;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Filament\Facades\Filament;
use Livewire\Livewire;

function homePhraseAdmin(): User
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

test('the home phrase resource is scoped to the home_phrases group', function (): void {
    $user = homePhraseAdmin();

    LanguageLine::query()->create([
        'group' => LanguageLine::HOME_PHRASES_GROUP,
        'key' => 'from_caracas',
        'text' => ['en' => 'from Caracas', 'es' => 'caraqueño'],
    ]);

    LanguageLine::query()->create([
        'group' => 'resources',
        'key' => 'something_else',
        'text' => ['en' => 'Something else'],
    ]);

    Livewire::actingAs($user)
        ->test(ListHomePhrases::class)
        ->call('loadTable')
        ->assertOk()
        ->assertCanSeeTableRecords([
            LanguageLine::query()->where('group', LanguageLine::HOME_PHRASES_GROUP)->first(),
        ])
        ->assertCanNotSeeTableRecords([
            LanguageLine::query()->where('group', 'resources')->first(),
        ]);
});

test('LanguageLine::getActivePhrasesByLocaleForGroup returns one bucket per active locale', function (): void {
    LanguageLine::query()->create([
        'group' => LanguageLine::HOME_PHRASES_GROUP,
        'key' => 'from_caracas',
        'text' => ['en' => 'from Caracas', 'es' => 'caraqueño'],
        'is_active' => true,
    ]);

    LanguageLine::query()->create([
        'group' => LanguageLine::HOME_PHRASES_GROUP,
        'key' => 'ssh_enjoyer',
        'text' => ['en' => 'and an SSH enjoyer', 'es' => ''],
        'is_active' => true,
    ]);

    LanguageLine::query()->create([
        'group' => LanguageLine::HOME_PHRASES_GROUP,
        'key' => 'hidden',
        'text' => ['en' => 'should not appear'],
        'is_active' => false,
    ]);

    $phrases = LanguageLine::getActivePhrasesByLocaleForGroup(LanguageLine::HOME_PHRASES_GROUP);

    expect($phrases)->toBeArray()
        ->and($phrases['en'])->toContain('from Caracas', 'and an SSH enjoyer')
        ->and($phrases['en'])->not->toContain('should not appear')
        ->and($phrases['es'])->toContain('caraqueño', 'and an SSH enjoyer');
});
