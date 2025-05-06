<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Spatie\Permission\Models\Permission;

class PermissionRegistrarServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $permissions = [
            'access app',
            'access home',
            'manage posts',
            'manage products',
            'approve payments',
            // Add all other custom permissions here
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, config('permission.default_guard_name'));
        }
    }
}
