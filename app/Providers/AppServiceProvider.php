<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\User;
use App\Services\InfoMail\Contracts\SmsProviderInterface;
use App\Services\InfoMail\SmsService;
use App\Settings\GeneralSettings;
use Carbon\CarbonImmutable;
use Exception;
use Filament\Actions\CreateAction;
use Filament\Actions\ExportAction;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Sleep;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Laravel\Octane\Facades\Octane;

final class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(SmsProviderInterface::class, SmsService::class);
        $this->app->bind(Authenticatable::class, User::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();
        $this->configureRateLimiting();
        $this->configureFilament();
        $this->configureGates();
        Page::$reportValidationErrorUsing = function (ValidationException $exception): void {
            Notification::make()
                ->title($exception->getMessage())
                ->danger()
                ->send();
        };
        $this->app->booted(function (): void {
            try {
                $settings = resolve(GeneralSettings::class);

                config([
                    'laravelpwa.manifest.theme_color' => $settings->pwa_theme_color,
                    'laravelpwa.manifest.background_color' => $settings->pwa_background_color,
                ]);
            } catch (Exception) {
            }
        });
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    private function configureDefaults(): void
    {
        Sleep::fake();
        Model::shouldBeStrict();
        Model::automaticallyEagerLoadRelationships();
        Date::use(CarbonImmutable::class);
        Password::defaults(
            Password::min(12)
                ->max(21)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised(3)
        );
        if (config('app.env') === 'production') {
            URL::forceHttps();
            // Http::preventStrayRequests();
            DB::prohibitDestructiveCommands();
            DB::reconnect();
            Octane::tick('reconnect-database', DB::reconnect(...), 300);
        }

        Vite::useAggressivePrefetching();
    }

    private function configureFilament(): void
    {
        Table::configureUsing(function (Table $table): void {
            $table
                ->emptyStateHeading(__('app.no_data'))
                ->emptyStateDescription(__('app.empty_list'))
                ->emptyStateActions([
                    CreateAction::make('create')
                        ->label(__('app.create'))
                        ->icon(Heroicon::Plus)
                        ->button(),
                ])
                ->striped()
                ->poll('10s')
                ->defaultPaginationPageOption(6)
                ->paginated([6, 24, 64, 86, 'all'])
                ->extremePaginationLinks()
                ->deferLoading()
                ->persistFiltersInSession()
                ->defaultSort('created_at', 'desc');
        });
        ExportAction::configureUsing(fn (ExportAction $action): ExportAction => $action->fileDisk('s3'));
    }

    private function configureGates(): void
    {
        Gate::define('viewPulse', fn (User $user): bool => $user->can('pulse.view'));
        Gate::define('viewSmartCache', fn (User $user): bool => $user->can('smart-cache.view'));
    }

    /**
     * Configure the rate limiters for the application.
     */
    private function configureRateLimiting(): void
    {
        // Default API rate limiter - 60 requests per minute
        RateLimiter::for('api', fn (Request $request) => Limit::perMinute(60)->by($request->user()?->id ?: $request->ip()));

        // Auth endpoints - more restrictive (prevent brute force)
        RateLimiter::for('auth', fn (Request $request) => Limit::perMinute(5)->by($request->ip()));

        // Authenticated user requests - higher limit
        RateLimiter::for('authenticated', fn (Request $request) => $request->user()
            ? Limit::perMinute(120)->by($request->user()->id)
            : Limit::perMinute(60)->by($request->ip()));
    }
}
