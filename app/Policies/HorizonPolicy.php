<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

final class HorizonPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the Horizon dashboard.
     */
    public function viewHorizon(User $user): bool
    {
        return $user->can('horizon.view');
    }
}
