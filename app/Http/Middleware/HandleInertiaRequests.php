<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use Override;

final class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    #[Override]
    protected $rootView = 'app';

    /**
     * Determine the root view based on the component framework.
     *
     * Checks both the route parameter (initial page load) and the
     * X-Inertia-Component header (client-side navigation).
     */
    public function rootView(Request $request): string
    {
        $component = (string) ($request->route('component') ?? $request->header('X-Inertia-Component', ''));

        if (str_starts_with($component, 'react/')) {
            return 'app-react';
        }

        if (str_starts_with($component, 'vue/')) {
            return 'app-vue';
        }

        if (str_starts_with($component, 'svelte/')) {
            return 'app-svelte';
        }

        return 'app';
    }

    /**
     * Determines the current asset version.
     *
     * Forces a full page reload when switching between frameworks
     * by returning a different version string for each framework.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): string
    {
        $component = (string) ($request->route('component') ?? $request->header('X-Inertia-Component', ''));

        // Return framework-specific version to force reload on framework change
        if (str_starts_with($component, 'react/')) {
            return parent::version($request) . '-react';
        }

        if (str_starts_with($component, 'vue/')) {
            return parent::version($request) . '-vue';
        }

        if (str_starts_with($component, 'svelte/')) {
            return parent::version($request) . '-svelte';
        }

        return parent::version($request) . '-react';
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'auth' => [
                'user' => $request->user(),
            ],
        ];
    }
}
