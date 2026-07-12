<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Laravel\Horizon\HorizonApplicationServiceProvider;
use Throwable;

final class HorizonServiceProvider extends HorizonApplicationServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        parent::boot();

        // Horizon::routeSmsNotificationsTo('15556667777');
        // Horizon::routeMailNotificationsTo('example@example.com');
        // Horizon::routeSlackNotificationsTo('slack-webhook-url', '#channel');
    }

    /**
     * Register the Horizon gate.
     *
     * This gate determines who can access Horizon in non-local environments.
     */
    protected function gate(): void
    {
        Gate::define('viewHorizon', function (?User $user = null): bool {
            if (!$user instanceof User) {
                return false;
            }

            if ($user->email === config('bizkit.admin_email')) {
                return true;
            }

            if (config('permission.teams') && $user->current_team_id !== null && function_exists('setPermissionsTeamId')) {
                setPermissionsTeamId($user->current_team_id);
            }

            try {
                return $user->hasPermissionTo('view_horizon');
            } catch (Throwable) {
                return false;
            }
        });
    }
}
