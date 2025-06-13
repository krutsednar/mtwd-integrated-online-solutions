<?php

namespace App\Providers\Filament;

use Auth;
use Filament\Pages;
use Filament\Panel;
use Filament\Widgets;
use Filament\PanelProvider;
use App\Filament\Pages\Profile;
use Filament\Support\Colors\Color;
use Filament\Navigation\NavigationItem;
use Filament\Http\Middleware\Authenticate;
use Illuminate\Session\Middleware\StartSession;
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

class ExecutivePanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('executive')
            ->path('executive')
            ->favicon(asset('images/mtwdicon.png'))
            ->login(\App\Filament\Pages\Auth\RedirectLogin::class)
            ->colors([
                'primary' => Color::Blue,
            ])
            ->profile(Profile::class, isSimple: false)
            ->defaultAvatarProvider(GetAvatarProvider::class)
            ->discoverResources(in: app_path('Filament/Executive/Resources'), for: 'App\\Filament\\Executive\\Resources')
            ->discoverPages(in: app_path('Filament/Executive/Pages'), for: 'App\\Filament\\Executive\\Pages')
            ->pages([
                // Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Executive/Widgets'), for: 'App\\Filament\\Executive\\Widgets')
            ->widgets([
                // Widgets\AccountWidget::class,
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
