<?php

namespace App\Filament\Bk;

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
use Illuminate\View\Middleware\ShareErrorsFromSession;

class BkPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('bk')
            ->path('bk')
            ->login()
            ->colors([
                'primary' => Color::hex('#FF9B51'),
                'gray' => Color::hex('#25343F'),
                'danger' => Color::hex('#E57373'),
                'success' => Color::hex('#4CAF50'),
                'warning' => Color::hex('#FF9B51'),
                'info' => Color::hex('#64B5F6'),
            ])
            ->font('Plus Jakarta Sans')
            ->brandName('Habitify BK')
            ->brandLogo(fn () => view('filament.logo'))
            ->favicon(asset('favicon.ico'))
            ->sidebarCollapsibleOnDesktop()
            ->navigationGroups([
                'Dashboard',
                'Laporan',
                'Expert System',
                'Konseling',
                'Data Santri',
            ])
            ->discoverResources(in: app_path('Filament/Bk/Resources'), for: 'App\\Filament\\Bk\\Resources')
            ->discoverPages(in: app_path('Filament/Bk/Pages'), for: 'App\\Filament\\Bk\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Bk/Widgets'), for: 'App\\Filament\\Bk\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
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
            ->databaseNotifications()
            ->databaseNotificationsPolling('30s');
    }
}