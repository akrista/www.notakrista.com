<?php

declare(strict_types=1);

use App\Enums\TeamRole;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $username = $this->stringConfig('bizkit.admin_username', 'admin');
        $email = $this->stringConfig('bizkit.admin_email', 'admin@example.com');
        $password = $this->stringConfig('bizkit.admin_password', 'password');

        if (User::query()->where('email', $email)->orWhere('username', $username)->exists()) {
            return;
        }

        $user = User::query()->create([
            'username' => $username,
            'firstname' => 'Admin',
            'lastname' => 'User',
            'email' => $email,
            'password' => Hash::make($password),
            'email_verified_at' => now(),
        ]);

        $team = Team::query()->create([
            'name' => 'Admin Team',
            'is_personal' => true,
        ]);

        $team->members()->attach($user->id, ['role' => TeamRole::Owner->value]);

        $user->update(['current_team_id' => $team->id]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $username = config('bizkit.admin_username') ?: 'admin';
        $email = config('bizkit.admin_email') ?: 'admin@example.com';

        $user = User::query()->where('email', $email)->orWhere('username', $username)->first();

        if ($user) {
            $teams = $user->teams()->get();
            foreach ($teams as $team) {
                $team->members()->detach($user->id);
                if ($team->is_personal) {
                    $team->forceDelete();
                }
            }

            $user->forceDelete();
        }
    }

    private function stringConfig(string $key, string $default): string
    {
        $value = config($key);

        return is_string($value) && $value !== '' ? $value : $default;
    }
};
