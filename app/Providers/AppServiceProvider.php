<?php

namespace App\Providers;

use Illuminate\Auth\SessionGuard;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(MessageService::class, function ($app) {
            return new MessageService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Auth::extend('session', function ($app, $name, array $config) {
            $guard = new SessionGuard(
                $name,
                Auth::createUserProvider($config['provider']),
                $app['session.store']
            );

            $guard->setCookieJar($app['cookie']);
            $guard->setDispatcher($app['events']);

            //  2 days = 60 * 24 * 2 = 2880 minutes
            $guard->setRememberDuration(60 * 24 * 2);

            return $guard;
        });
    }
}
