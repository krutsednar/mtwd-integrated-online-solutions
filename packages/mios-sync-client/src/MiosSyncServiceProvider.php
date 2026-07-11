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

        foreach ((array) config('mios-sync.observe') as $model => $resource) {
            if (class_exists($model)) {
                $model::observe(new OutboxObserver(is_array($resource) ? $resource['resource'] : $resource));
            }
        }
    }
}
