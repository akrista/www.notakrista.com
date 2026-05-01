<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

final class SmartCachePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the Smart Cache dashboard.
     */
    public function viewSmartCache(User $user): bool
    {
        return $user->can('smart-cache.view');
    }
}
