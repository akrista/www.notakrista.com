<?php

declare(strict_types=1);

namespace App\Providers\Filament;

use App\Filament\Pages\Auth\EditProfile;
use App\Filament\Pages\Auth\Login;
use App\Http\Middleware\SetLocale;
use App\Settings\GeneralSettings;
use App\Support\LanguageSwitcher;
use Exception;
use Filament\Auth\MultiFactor\App\AppAuthentication;
use Filament\Auth\MultiFactor\Email\EmailAuthentication;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Platform;
use Filament\View\PanelsRenderHook;
use Illuminate\Contracts\View\View;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\Middleware\ShareErrorsFromSession;

final class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        $settings = $this->getSettings();

        return $panel
            ->default()
            ->id(config('fillakit.panel_route'))
            ->path(config('fillakit.only_filament') ? '/' : '/' . config('fillakit.panel_route'))
            ->profile(
                page: EditProfile::class,
                isSimple: false
            )
            ->multiFactorAuthentication([
                AppAuthentication::make()
                    ->brandName($settings?->brand_name ?? config('app.name'))
                    ->codeWindow(6)
                    ->recoverable()
                    ->regenerableRecoveryCodes(),
                EmailAuthentication::make()
                    ->codeExpiryMinutes(4),
            ], isRequired: false)
            ->login(action: Login::class)
            ->loginRouteSlug('login')
            // ->registration(action: Register::class)
            // ->registrationRouteSlug('register')
            ->passwordReset()
            // ->passwordResetRoutePrefix('password-reset')
            // ->passwordResetRequestRouteSlug('request')
            // ->passwordResetRouteSlug('reset')
            ->emailVerification()
            ->emailVerificationRouteSlug('verify')
            ->emailVerificationRoutePrefix('email-verification')
            ->emailVerificationPromptRouteSlug('prompt')
            // ->emailChangeVerification()
            // ->emailChangeVerificationRoutePrefix('email-change-verification')
            // ->emailChangeVerificationRouteSlug('verify')
            ->revealablePasswords(true)
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->colors(function (): array {
                try {
                    $settings = $this->getSettings();
                    if (!$settings instanceof GeneralSettings) {
                        return [];
                    }

                    return array_filter(array_map(
                        Color::generateV3Palette(...),
                        array_filter($settings->site_theme ?? [])
                    ));
                } catch (Exception) {
                    return [];
                }
            })
            ->brandName(function (): string {
                try {
                    $settings = $this->getSettings();

                    return $settings?->brand_name ?? config('app.name');
                } catch (Exception) {
                    return config('app.name');
                }
            })
            ->brandLogo(function () {
                try {
                    $settings = $this->getSettings();

                    return Storage::disk('public')->exists($settings->brand_logo) ? Storage::url($settings->brand_logo) : null;
                } catch (Exception) {
                    return null;
                }
            })
            ->favicon(function () {
                try {
                    $settings = $this->getSettings();
                    if (! $settings || in_array($settings->site_favicon ?? null, [null, '', '0'], true)) {
                        return null;
                    }

                    return Storage::url($settings->site_favicon);
                } catch (Exception) {
                    return null;
                }
            })
            ->brandLogoHeight(function (): string {
                try {
                    $settings = $this->getSettings();
                    if (! $settings || ! $settings->brand_logo_height || ! $settings->brand_logo_height_unit) {
                        return '2rem';
                    }

                    return $settings->brand_logo_height . $settings->brand_logo_height_unit;
                } catch (Exception) {
                    return '2rem';
                }
            })
            ->globalSearchKeyBindings(['command+shift+f', 'ctrl+shift+f'])
            ->globalSearchFieldSuffix(
                fn(): string => match (Platform::detect()) {
                    Platform::Windows,
                    Platform::Linux => 'Ctrl+Shift+F',
                    Platform::Mac => '⌘+⇧+F',
                    default => 'Ctrl+Shift+F',
                }
            )
            ->topbar(config('fillakit.topbar_enabled'))
            ->topNavigation(config('fillakit.top_nav_enabled'))
            ->sidebarCollapsibleOnDesktop(!config('fillakit.top_nav_enabled'))
            ->spa(condition: true, hasPrefetching: true)
            ->renderHook(
                name: PanelsRenderHook::HEAD_START,
                hook: fn (): View => view('components.seo.meta'),
            )
            ->renderHook(
                name: PanelsRenderHook::BODY_END,
                hook: fn (): View => view('filament.switcher.switcher'),
            )
            ->renderHook(
                name: PanelsRenderHook::FOOTER,
                hook: fn (): View => view('filament.footer.footer'),
            )
            ->unsavedChangesAlerts()
            ->databaseNotifications()
            ->databaseNotificationsPolling('60s')
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->discoverClusters(in: app_path('Filament/Clusters'), for: 'App\\Filament\\Clusters')
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->middleware([SetLocale::class], isPersistent: true)
            ->renderHook(
                name: PanelsRenderHook::USER_MENU_PROFILE_AFTER,
                hook: fn (): View => view('filament.language-switcher.language-switcher', LanguageSwitcher::getViewData()),
            );
    }

    private function getSettings(): ?GeneralSettings
    {
        try {
            if (! $this->hasSettingsTable()) {
                return null;
            }

            return resolve(GeneralSettings::class);
        } catch (Exception) {
            return null;
        }
    }

    private function hasSettingsTable(): bool
    {
        try {
            return DB::connection()->getSchemaBuilder()->hasTable('settings');
        } catch (Exception) {
            return false;
        }
    }
}
