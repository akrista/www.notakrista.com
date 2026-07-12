<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\Team;
use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class SyncSpatiePermissionsWithFilamentTenants
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $tenant = Filament::getTenant();

        if ($tenant instanceof Team && auth()->check()) {
            $tenantId = (string) $tenant->getKey();

            if (getPermissionsTeamId() !== $tenantId) {
                setPermissionsTeamId($tenantId);

                $user = auth()->user();
                if ($user !== null) {
                    $user->unsetRelation('roles');
                    $user->unsetRelation('permissions');
                }
            }
        }

        return $next($request);
    }
}
