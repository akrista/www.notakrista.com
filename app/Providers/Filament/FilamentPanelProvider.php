<?php

declare(strict_types=1);

namespace App\Providers\Filament;

use App\Enums\FilamentMode;
use App\Filament\Pages\Auth\EditProfile;
use App\Filament\Pages\Auth\Login;
use App\Filament\Pages\Auth\Register;
use App\Filament\Pages\Tenancy\RegisterTeam;
use App\Http\Middleware\SyncSpatiePermissionsWithFilamentTenants;
use App\Models\Team;
use Filament\Auth\MultiFactor\App\AppAuthentication;
use Filament\Auth\MultiFactor\Email\EmailAuthentication;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Platform;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

final class FilamentPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('filament')
            ->path(FilamentMode::fromConfig()->panelPath())
            ->registration(
                action: Register::class
            )
            ->registrationRouteSlug('register')
            ->login(
                action: Login::class
            )
            ->loginRouteSlug('login')

            ->profile(
                page: EditProfile::class,
                isSimple: false
            )
            ->multiFactorAuthentication([
                AppAuthentication::make()
                    ->brandName(is_string(config('app.name')) ? config('app.name') : null)
                    ->codeWindow(6)
                    ->recoverable()
                    ->regenerableRecoveryCodes(),
                EmailAuthentication::make()
                    ->codeExpiryMinutes(4),
            ], isRequired: false)
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
            ->tenant(model: Team::class, slugAttribute: 'slug', ownershipRelationship: 'owner')
            ->tenantRegistration(RegisterTeam::class)
            ->tenantSwitcher()
            ->tenantMenu()
            ->searchableTenantMenu()
            ->tenantMiddleware([
                SyncSpatiePermissionsWithFilamentTenants::class,
            ], isPersistent: true)
            // ->font()
            ->viteTheme('resources/css/filament/filament/theme.css')
            ->colors([
                'primary' => Color::Amber,
            ])
            ->globalSearchKeyBindings(['command+shift+f', 'ctrl+shift+f'])
            ->globalSearchFieldSuffix(
                fn(): string => match (Platform::detect()) {
                    Platform::Windows,
                    Platform::Linux => 'Ctrl+Shift+F',
                    Platform::Mac => '⌘+⇧+F',
                    default => 'Ctrl+Shift+F',
                }
            )
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
            ])
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
            ]);
    }
}
