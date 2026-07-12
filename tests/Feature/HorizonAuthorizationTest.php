<?php

declare(strict_types=1);

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;

test('guests are unauthorized to view horizon dashboard', function (): void {
    $response = $this->get('/horizon');

    $response->assertStatus(403);
});

test('configured superadmin is authorized to view horizon dashboard', function (): void {
    $adminEmail = config('bizkit.admin_email') ?: 'admin@example.com';
    $user = User::query()->where('email', $adminEmail)->first()
        ?? User::factory()->create(['email' => $adminEmail]);

    $response = $this->actingAs($user)->get('/horizon');

    $response->assertOk();
});

test('users with view_horizon permission are authorized to view horizon dashboard', function (): void {
    $user = User::factory()->create();
    $team = $user->personalTeam();
    $user->update(['current_team_id' => $team->id]);

    setPermissionsTeamId($team->id);
    $role = Role::create(['name' => 'staff', 'guard_name' => 'web', 'team_id' => $team->id]);
    $permission = Permission::query()->firstOrCreate(['name' => 'view_horizon', 'guard_name' => 'web']);
    $role->givePermissionTo($permission);
    $user->assignRole($role);

    $response = $this->actingAs($user)->get('/horizon');

    $response->assertOk();
});

test('users without view_horizon permission are unauthorized to view horizon dashboard', function (): void {
    $user = User::factory()->create();
    $team = $user->personalTeam();
    $user->update(['current_team_id' => $team->id]);

    $response = $this->actingAs($user)->get('/horizon');

    $response->assertStatus(403);
});
