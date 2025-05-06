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
use Rmsramos\Activitylog\ActivitylogPlugin;
use App\Filament\Resources\PermissionResource;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Filament\Http\Middleware\AuthenticateSession;
use Monzer\FilamentChatifyIntegration\ChatifyPlugin;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use BezhanSalleh\FilamentShield\Resources\RoleResource;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            // ->login()
            ->colors([
                'primary' => Color::Blue,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
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
                ActivitylogPlugin::make()
                ->navigationGroup('User Management')
                ->navigationIcon('mdi-shield-account-outline')
                ->navigationItem(fn () => auth()->check() && auth()->user()->hasRole('super_admin'))
                // ->visible(fn () => auth()->user()?->hasRole('super_admin'))
                ,
            ])
            ->sidebarCollapsibleOnDesktop()
            // ->resources([
            //     RoleResource::class,
            // ])
            ->databaseNotifications()
            ->navigationItems([
                NavigationItem::make('Messenger')
                    ->url(url('messenger'), shouldOpenInNewTab: true)
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->badge(
                        fn () => auth()->check() ? auth()->user()->getUnreadCount() : null
                    )
                    ->sort(1),
            ]);
            // ->plugin(ChatifyPlugin::make());

    }
}
