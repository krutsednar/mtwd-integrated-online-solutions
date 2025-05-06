<?php

namespace App\Providers\Filament;

use Filament\Pages;
use Filament\Panel;
use Filament\Widgets;
use Filament\PanelProvider;
use Filament\Navigation\MenuItem;
use Filament\Support\Colors\Color;
use Filament\Navigation\NavigationItem;
use Rupadana\ApiService\ApiServicePlugin;
use Filament\Http\Middleware\Authenticate;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Filament\Http\Middleware\AuthenticateSession;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;

class MOJOPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('MOJO')
            ->path('MOJO')
            ->favicon(asset('images/mtwdlogo.png'))
            ->colors([
                'primary' => Color::Blue,
            ])
            ->sidebarCollapsibleOnDesktop()
            ->discoverResources(in: app_path('Filament/MOJO/Resources'), for: 'App\\Filament\\MOJO\\Resources')
            ->discoverPages(in: app_path('Filament/MOJO/Pages'), for: 'App\\Filament\\MOJO\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/MOJO/Widgets'), for: 'App\\Filament\\MOJO\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
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
            ->plugins([
                ApiServicePlugin::make(),
                // \BezhanSalleh\FilamentShield\FilamentShieldPlugin::make(),
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
            ->userMenuItems([
                'Messenger' => MenuItem::make()->label('Messenger')
                                ->url('http://localhost:8000/messenger')
                                ->icon('heroicon-o-chat-bubble-left-right'),
                'logout' => MenuItem::make()->label('Log out'),
                // ...
            ]);
    }

}
