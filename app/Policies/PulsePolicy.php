<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

final class PulsePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the Pulse dashboard.
     */
    public function viewPulse(User $user): bool
    {
        return $user->can('pulse.view');
    }
}
