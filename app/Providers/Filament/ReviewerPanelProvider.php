<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Jeffgreco13\FilamentBreezy\BreezyCore;

class ReviewerPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('reviewer')
            ->path('reviewer')
            ->colors([
                'primary' => Color::Amber,
            ])
            ->brandName(config('app.name') . ' - Reviewer')
            ->login()
            ->passwordReset()
            ->emailVerification()
            ->profile()
            ->breadcrumbs(false)
            ->discoverResources(in: app_path('Filament/Reviewer/Resources'), for: 'App\\Filament\\Reviewer\\Resources')
            ->discoverPages(in: app_path('Filament/Reviewer/Pages'), for: 'App\\Filament\\Reviewer\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Reviewer/Widgets'), for: 'App\\Filament\\Reviewer\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->authGuard('web')
            ->plugin(
                BreezyCore::make()
                    ->enableTwoFactorAuthentication()
                    ->passwordUpdateRules(
                        rules: [Password::default()->mixedCase()],
                        requiresCurrentPassword: true,
                    )
                    ->myProfile(
                        shouldRegisterUserMenu: true,
                    ),
            );
    }
}
