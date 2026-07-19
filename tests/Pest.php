<?php

declare(strict_types=1);

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind different classes or traits.
|
*/

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', fn () => $this->toBe(1));

/*
|--------------------------------------------------------------------------
| Test Hooks
|--------------------------------------------------------------------------
*/

// Force every Feature test to use an isolated in-memory SQLite database,
// regardless of what `.env` or the host shell sets. This keeps feature tests
// hermetic so the dev file is never mutated by tests.
beforeEach(function (): void {
    config([
        'database.default' => 'sqlite',
        'database.connections.sqlite.database' => ':memory:',
    ]);
    app()->forgetInstance('db');
    app()->forgetInstance('db.connection');
    DB::purge('sqlite');
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function something(): void
{
    // ..
}

/**
 * Create an admin user on a personal team with the given Spatie permissions
 * and authenticate them against the Filament panel. Used across the
 * budget/account/transaction/schedule Filament resource tests.
 */
function budgetAdmin(array $permissions = []): User
{
    $user = User::factory()->create();
    $team = $user->personalTeam();

    setPermissionsTeamId($team->id);
    $role = Role::query()->firstOrCreate([
        'name' => 'admin',
        'guard_name' => 'web',
        'team_id' => $team->id,
    ]);

    $perms = collect($permissions)->map(
        fn (string $name): Permission => Permission::query()->firstOrCreate([
            'name' => $name,
            'guard_name' => 'web',
        ]),
    );

    if ($perms->isNotEmpty()) {
        $role->givePermissionTo($perms->all());
        $user->assignRole($role);
    }

    test()->actingAs($user);

    Filament::setCurrentPanel(Filament::getPanel('filament'));
    Filament::setTenant($team);

    return $user;
}
