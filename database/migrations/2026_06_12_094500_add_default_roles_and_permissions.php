<?php

declare(strict_types=1);

use App\Models\Role;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $email = config('bizkit.admin_email') ?: 'admin@example.com';
        $user = User::query()->where('email', $email)->first();

        if ($user === null) {
            return;
        }

        $team = $user->personalTeam() ?? Team::query()->first();

        if ($team === null) {
            return;
        }

        setPermissionsTeamId($team->id);

        $role = Role::query()->firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'web',
            'team_id' => $team->id,
        ]);

        $user->assignRole($role);

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $email = config('bizkit.admin_email') ?: 'admin@example.com';
        $user = User::query()->where('email', $email)->first();

        if ($user === null) {
            return;
        }

        $team = $user->personalTeam() ?? Team::query()->first();

        if ($team === null) {
            return;
        }

        setPermissionsTeamId($team->id);

        $role = Role::query()->where([
            'name' => 'admin',
            'guard_name' => 'web',
            'team_id' => $team->id,
        ])->first();

        if ($role !== null) {
            $user->removeRole($role);
            $role->delete();
        }

    }
};
