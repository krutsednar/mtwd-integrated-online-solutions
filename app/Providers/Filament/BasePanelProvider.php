<?php

namespace App\Providers\Filament;

use Filament\Panel;
use Filament\PanelProvider;
use App\Http\Middleware\RedirectIfCannotAccessPanel;

abstract class BasePanelProvider extends PanelProvider
{
    protected function applyBaseMiddleware(Panel $panel): Panel
    {
        return $panel->middleware([
            RedirectIfCannotAccessPanel::class,
        ]);
    }
}
