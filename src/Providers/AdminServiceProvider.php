<?php

namespace Geeklearners\Providers;

use Illuminate\Support\ServiceProvider;

class AdminServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadMigrationsFrom(dirname(__DIR__) . "/database/Migration");
        $this->loadRoutesFrom(dirname(__DIR__) . '/Route/web.php');
        $this->loadViewsFrom(dirname(__DIR__) . "/Resources/Views", "admin");
        $this->publishes([dirname(__DIR__) . '/Config/admin.php' => config_path('admin.php')], "geeklearners_admin");
        $this->publishes([dirname(__DIR__) . '/Resources/Views' => resource_path('views/vendor/admin')]);
    }
    public function register()
    {
        $this->mergeConfigFrom(dirname(__DIR__) . '/Config/admin.php', 'admin');
    }
}
