<?php

namespace App\Providers;

use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use BezhanSalleh\FilamentShield\Commands;
use BezhanSalleh\PanelSwitch\PanelSwitch;
use Filament\Support\Facades\FilamentView;
use BezhanSalleh\FilamentShield\FilamentShield;
use App\Http\Responses\LogoutResponse;
use Filament\Http\Responses\Auth\Contracts\LogoutResponse as LogoutResponseContract;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(LogoutResponseContract::class, LogoutResponse::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        PanelSwitch::configureUsing(function (PanelSwitch $panelSwitch) {
            $panelSwitch->panels([
                'home',
                'MOJO',
                'PFIS',
                'HRIS',
                'MCIS',
                'MOCA',
                'executive',
                'admin',

            ])
            // ->heading('MTWD Online Information Systems')
            ->modalWidth('sm')
            ->slideOver()
            ->icons([

                'home' => 'heroicon-o-home',
                'HRIS' => 'fluentui-folder-people-24-o',
                'MCIS' => 'css-user-list',
                'MOJO' => 'mdi-pipe-leak',
                'MOCA' => 'pepicon-cv',
                'PFIS' => 'fas-arrow-up-from-water-pump',
                'executive' => 'govicon-user-suit',
                'admin' => 'eos-admin-o',
            ])
            ->iconSize(16)
            ->labels([
                'admin' => 'Administrator',
                'home' => 'Home',
                // 'PFIS' => 'PaFIS',
            ])
            ->modalHeading('MTWD Information Systems');
        });

        // RenderHook::register('panels::topbar.start', function () {
        //     return '<button class="btn btn-primary">Custom Button</button>';
        // });

        FilamentView::registerRenderHook(
            PanelsRenderHook::GLOBAL_SEARCH_BEFORE,
            fn (): string => Blade::render('@livewire(\'buttons.messenger\')'),
            // return '<button class="btn btn-primary">Custom Button</button>',
        );

        // FilamentShield::prohibitDestructiveCommands($this->app->isProduction());

        $this->app->booted(function () {
            app(FilamentShield::class)->configurePermissionIdentifierUsing(
                fn ($resource) => str($resource::getModel())
                    ->afterLast('\\')
                    ->lower()
                    ->toString()
            );
        });

        Gate::guessPolicyNamesUsing(function (string $modelClass) {
            return str_replace('Models', 'Policies', $modelClass) . 'Policy';
        });
    }
}
