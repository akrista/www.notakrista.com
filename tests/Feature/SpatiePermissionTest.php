<?php

declare(strict_types=1);

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Str;

test('it creates roles and permissions with UUIDs', function (): void {
    $role = Role::create(['name' => 'editor', 'guard_name' => 'web']);
    $permission = Permission::create(['name' => 'edit posts', 'guard_name' => 'web']);

    expect($role->id)->toBeString()
        ->and(Str::isUuid($role->id))->toBeTrue()
        ->and($permission->id)->toBeString()
        ->and(Str::isUuid($permission->id))->toBeTrue();
});

test('it can assign permissions to roles and check them on users within a team scope', function (): void {
    $user = User::factory()->create();
    $team = $user->personalTeam();

    setPermissionsTeamId($team->id);

    $role = Role::create(['name' => 'admin', 'guard_name' => 'web']);
    $permission = Permission::create(['name' => 'delete users', 'guard_name' => 'web']);

    $role->givePermissionTo($permission);
    $user->assignRole($role);

    expect($user->hasRole('admin'))->toBeTrue()
        ->and($user->hasPermissionTo('delete users'))->toBeTrue();
});
