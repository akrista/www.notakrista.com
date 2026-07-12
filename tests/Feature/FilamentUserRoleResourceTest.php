<?php

declare(strict_types=1);

use App\Filament\Resources\Users\Pages\ManageUsers;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Filament\Facades\Filament;
use Livewire\Livewire;

test('authenticated users can access user resource page under a team tenant', function (): void {
    $user = User::factory()->create();
    $team = $user->personalTeam();

    setPermissionsTeamId($team->id);
    $role = Role::create(['name' => 'admin', 'guard_name' => 'web', 'team_id' => $team->id]);
    $permission = Permission::query()->firstOrCreate(['name' => 'view_any_user', 'guard_name' => 'web']);
    $role->givePermissionTo($permission);
    $user->assignRole($role);

    $response = $this->actingAs($user)->get(sprintf('/admin/%s/users', $team->slug));

    $response->assertOk();
});

test('authenticated users can access roles resource page under a team tenant', function (): void {
    $user = User::factory()->create();
    $team = $user->personalTeam();

    setPermissionsTeamId($team->id);
    $role = Role::create(['name' => 'admin', 'guard_name' => 'web', 'team_id' => $team->id]);
    $permission = Permission::query()->firstOrCreate(['name' => 'view_any_role', 'guard_name' => 'web']);
    $role->givePermissionTo($permission);
    $user->assignRole($role);

    $response = $this->actingAs($user)->get(sprintf('/admin/%s/roles', $team->slug));

    $response->assertOk();
});

test('authenticated users can access create role page under a team tenant', function (): void {
    $user = User::factory()->create();
    $team = $user->personalTeam();

    setPermissionsTeamId($team->id);
    $role = Role::create(['name' => 'admin', 'guard_name' => 'web', 'team_id' => $team->id]);
    $p1 = Permission::query()->firstOrCreate(['name' => 'view_any_role', 'guard_name' => 'web']);
    $p2 = Permission::query()->firstOrCreate(['name' => 'create_role', 'guard_name' => 'web']);
    $role->givePermissionTo($p1);
    $role->givePermissionTo($p2);

    $user->assignRole($role);

    $response = $this->actingAs($user)->get(sprintf('/admin/%s/roles/create', $team->slug));

    $response->assertOk();
});

test('it can render manage users livewire component and list users', function (): void {
    $user = User::factory()->create();
    $team = $user->personalTeam();

    setPermissionsTeamId($team->id);
    $role = Role::create(['name' => 'admin', 'guard_name' => 'web', 'team_id' => $team->id]);
    $permission = Permission::query()->firstOrCreate(['name' => 'view_any_user', 'guard_name' => 'web']);
    $role->givePermissionTo($permission);
    $user->assignRole($role);

    $this->actingAs($user);

    Filament::setCurrentPanel(Filament::getPanel('filament'));
    Filament::setTenant($team);

    Livewire::test(ManageUsers::class)
        ->call('loadTable')
        ->assertOk()
        ->assertCanSeeTableRecords([$user]);
});

test('it can create roles and associate permissions', function (): void {
    $user = User::factory()->create();
    $team = $user->personalTeam();

    // Set permission team scope
    setPermissionsTeamId($team->id);

    // Create a role under this team
    $role = Role::create([
        'name' => 'Manager',
        'guard_name' => 'web',
        'team_id' => $team->id,
    ]);

    expect(Role::query()->where('team_id', $team->id)->count())->toBe(1);
});

test('admin user sees users and roles in the navigation menu', function (): void {
    $user = User::factory()->create();
    $team = $user->personalTeam();

    setPermissionsTeamId($team->id);
    $role = Role::create(['name' => 'admin', 'guard_name' => 'web', 'team_id' => $team->id]);
    $p1 = Permission::query()->firstOrCreate(['name' => 'view_any_user', 'guard_name' => 'web']);
    $p2 = Permission::query()->firstOrCreate(['name' => 'view_any_role', 'guard_name' => 'web']);
    $role->givePermissionTo($p1);
    $role->givePermissionTo($p2);

    $user->assignRole($role);

    $response = $this->actingAs($user)->get('/admin/' . $team->slug);

    $response->assertOk();
    $response->assertSee('Users');
    $response->assertSee('Roles');
});
