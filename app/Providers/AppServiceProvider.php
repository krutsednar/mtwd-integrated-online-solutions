<?php

namespace App\Providers;

use Filament\Facades\Filament;
use App\Filament\Pages\Profile;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\URL;
use App\Http\Responses\LoginResponse;
use Illuminate\Support\Facades\Blade;
use App\Http\Responses\LogoutResponse;
use Illuminate\Support\ServiceProvider;
use BezhanSalleh\PanelSwitch\PanelSwitch;
use Filament\Support\Facades\FilamentView;
use Filament\Http\Responses\Auth\Contracts\LogoutResponse as LogoutResponseContract;
use Filament\Http\Responses\Auth\Contracts\LoginResponse as LoginResponseContract;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(LogoutResponseContract::class, LogoutResponse::class);
        $this->app->singleton(LoginResponseContract::class, LoginResponse::class);

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        PanelSwitch::configureUsing(function (PanelSwitch $panelSwitch) {
            $panelSwitch->panels([
                'home',
                'MOJO',
                'PFIS',
                'HRIS',
                'MCIS',
                'MOCA',
                'executive',
                // 'admin',

            ])
            // ->visible(fn (): bool => auth()->user()?->hasAnyRole([
            //     'super_admin',
            // ]))
            // ->heading('MTWD Online Information Systems')
            ->modalWidth('sm')
            ->slideOver()
            ->icons([

                'home' => 'heroicon-o-home',
                'HRIS' => 'fas-users-rectangle',
                'MCIS' => 'css-user-list',
                'MOJO' => 'mdi-pipe-leak',
                'MOCA' => 'fas-user-plus',
                'PFIS' => 'fas-arrow-up-from-water-pump',
                'executive' => 'fas-user-tie',
                'admin' => 'fas-user-secret',
            ])
            ->iconSize(16)
            ->labels([
                'admin' => 'Administrator',
                'home' => 'Home',
                // 'PFIS' => 'PaFIS',
            ])
            ->modalHeading('MTWD Information Systems');
        });

        FilamentView::registerRenderHook(
            PanelsRenderHook::GLOBAL_SEARCH_BEFORE,
            fn (): string => Blade::render('@livewire(\'buttons.messenger\')'),
        );
        FilamentView::registerRenderHook(
            PanelsRenderHook::SIDEBAR_FOOTER,
            fn (): string => Blade::render('@livewire(\'buttons.logout\')'),
        );
    }
}
