<?php

declare(strict_types=1);

use App\Enums\TeamPermission;
use App\Enums\TeamRole;
use App\Models\Membership;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;

test('bizkit:generate-policies command generates policy files', function (): void {
    $policiesPath = app_path('Policies');
    $testPolicyFile = $policiesPath . '/MembershipPolicy.php';

    // Ensure clean state for the test
    if (File::exists($testPolicyFile)) {
        File::delete($testPolicyFile);
    }

    $exitCode = Artisan::call('bizkit:generate-policies');

    expect($exitCode)->toBe(0)
        ->and(File::exists($testPolicyFile))->toBeTrue();

    // Verify generated content contains standard Spatie mapping
    $content = File::get($testPolicyFile);
    expect($content)->toContain(Membership::class)
        ->and($content)->toContain('view_any_membership');

    // Clean up
    File::delete($testPolicyFile);
});

test('bizkit:sync-permissions command seeds permissions and syncs to admin roles', function (): void {
    $team = Team::factory()->create(['name' => 'Test Team']);
    $role = Role::create([
        'name' => 'admin',
        'guard_name' => 'web',
        'team_id' => $team->id,
    ]);

    $exitCode = Artisan::call('bizkit:sync-permissions');

    expect($exitCode)->toBe(0);

    // Verify that at least one custom permission was seeded in the DB
    $hasPulsePermission = Permission::query()->where('name', 'view_pulse')->exists();
    expect($hasPulsePermission)->toBeTrue();

    // Verify that the team admin role has permissions synced
    expect($role->hasPermissionTo('view_pulse'))->toBeTrue();
});

test('hasTeamPermission bridges Spatie permissions and fallback enums', function (): void {
    $user = User::factory()->create();
    $team = $user->personalTeam();

    setPermissionsTeamId($team->id);

    // Test Case 1: Database-backed permission exists and takes precedence
    $permission = Permission::query()->firstOrCreate(['name' => 'custom_db_perm', 'guard_name' => 'web']);
    $role = Role::create(['name' => 'custom-role', 'guard_name' => 'web', 'team_id' => $team->id]);
    $role->givePermissionTo($permission);

    $user->assignRole($role);

    expect($user->hasTeamPermission($team, 'custom_db_perm'))->toBeTrue();

    // Test Case 2: Fallback to hardcoded TeamRole enum mapping (e.g. Owner has all cases)
    // Make user Owner
    $team->members()->updateExistingPivot($user->id, ['role' => TeamRole::Owner->value]);
    expect($user->hasTeamPermission($team, TeamPermission::UpdateTeam))->toBeTrue();
});

test('Gate::before bypasses check for super admin email and admin role', function (): void {
    $adminEmail = 'superadmin@example.com';
    config(['bizkit.admin_email' => $adminEmail]);

    $superUser = User::factory()->create(['email' => $adminEmail]);

    // Super Admin by email should automatically pass all gates
    expect(Gate::forUser($superUser)->allows('random_ability'))->toBeTrue();

    // Test Role-based admin bypass
    $user = User::factory()->create();
    $team = $user->personalTeam();
    $user->update(['current_team_id' => $team->id]);

    setPermissionsTeamId($team->id);
    Role::create(['name' => 'admin', 'guard_name' => 'web', 'team_id' => $team->id]);
    $user->assignRole('admin');

    // User with admin role should automatically pass all gates
    expect(Gate::forUser($user)->allows('some_other_ability'))->toBeTrue();
});
