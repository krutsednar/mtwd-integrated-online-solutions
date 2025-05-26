<?php

namespace App\Providers\Filament;

use Filament\Pages;
use Filament\Panel;
use Filament\Widgets;
use Filament\PanelProvider;
use App\Filament\Pages\Profile;
use Filament\Support\Colors\Color;
use Filament\Navigation\NavigationItem;
use Filament\Http\Middleware\Authenticate;
use App\Filament\Widgets\OnlineJobOrderMap;
use App\Filament\MOJO\Widgets\JobOrdersChart;
use App\Filament\MOJO\Widgets\JobOrderOverview;
use Illuminate\Session\Middleware\StartSession;
use App\Filament\MOJO\Widgets\JobOrdersPerMonth;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Filament\Http\Middleware\AuthenticateSession;
use App\Filament\AvatarProviders\GetAvatarProvider;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Leandrocfe\FilamentApexCharts\FilamentApexChartsPlugin;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;

class MOJOPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('MOJO')
            ->path('MOJO')
            ->login(\App\Filament\Pages\Auth\RedirectLogin::class)
            ->colors([
                'primary' => Color::Blue,
            ])
            ->discoverResources(in: app_path('Filament/MOJO/Resources'), for: 'App\\Filament\\MOJO\\Resources')
            ->discoverPages(in: app_path('Filament/MOJO/Pages'), for: 'App\\Filament\\MOJO\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->profile(Profile::class, isSimple: false)
            ->defaultAvatarProvider(GetAvatarProvider::class)
            ->discoverWidgets(in: app_path('Filament/MOJO/Widgets'), for: 'App\\Filament\\MOJO\\Widgets')
            ->widgets([
                // Widgets\AccountWidget::class,
                JobOrderOverview::class,

                OnlineJobOrderMap::class,
                // Widgets\FilamentInfoWidget::class,
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
            ->navigationItems([
                NavigationItem::make('Messenger')
                    ->url(url('messenger'), shouldOpenInNewTab: true)
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->badge(
                        fn () => auth()->check() ? auth()->user()->getUnreadCount() : null
                    )
                    ->sort(1),

            ])
            ->plugins([
            FilamentApexChartsPlugin::make()
            ])
            ->sidebarCollapsibleOnDesktop();

    }
}
