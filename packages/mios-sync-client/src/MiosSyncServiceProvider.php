<?php

namespace Mtwd\MiosSyncClient;

use Illuminate\Support\ServiceProvider;

class MiosSyncServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/mios-sync.php', 'mios-sync');
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        $this->publishes([
            __DIR__.'/../config/mios-sync.php' => config_path('mios-sync.php'),
        ], 'mios-sync-config');

        if (! config('mios-sync.enabled')) {
            return;
        }

        // Register by CLASS NAME only — Eloquent re-resolves observers from the
        // container per event, so instances (and constructor args) don't survive.
        // The observer looks its resource slug up from config per event.
        foreach (array_keys((array) config('mios-sync.observe')) as $model) {
            if (class_exists($model)) {
                $model::observe(OutboxObserver::class);
            }
        }
    }
}
