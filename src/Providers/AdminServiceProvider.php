<?php

namespace Geeklearners\Providers;

use Geeklearners\Util\ViewRecorder;
use Illuminate\Support\Facades\Event;
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
        Event::listen('before.render', function () {
            $args = func_get_args();
            app()->make('ViewRecorder')->register($args[0], $args[1]);
        });
    }
    public function register()
    {
        $this->mergeConfigFrom(dirname(__DIR__) . '/Config/admin.php', 'admin');
        $this->app->singleton('ViewRecorder', function ($app) {
            return new ViewRecorder();
        });
    }
}
