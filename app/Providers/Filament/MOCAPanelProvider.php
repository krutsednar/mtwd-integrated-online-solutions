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
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Filament\Http\Middleware\AuthenticateSession;
use App\Filament\AvatarProviders\GetAvatarProvider;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;

class MOCAPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('MOCA')
            ->path('MOCA')
            ->favicon(asset('images/mios-logo.png'))
            ->login(\App\Filament\Pages\Auth\RedirectLogin::class)
            ->colors([
                'primary' => Color::Blue,
            ])
            ->profile(Profile::class, isSimple: false)
            ->defaultAvatarProvider(GetAvatarProvider::class)
            ->discoverResources(in: app_path('Filament/MOCA/Resources'), for: 'App\\Filament\\MOCA\\Resources')
            ->discoverPages(in: app_path('Filament/MOCA/Pages'), for: 'App\\Filament\\MOCA\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/MOCA/Widgets'), for: 'App\\Filament\\MOCA\\Widgets')
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
            ])->navigationItems([
                NavigationItem::make('Messenger')
                    ->url(url('messenger'), shouldOpenInNewTab: true)
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->badge(
                        fn () => auth()->check() ? auth()->user()->getUnreadCount() : null
                    )
                    ->sort(1),
            ]);
    }
}
