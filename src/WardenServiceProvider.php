<?php namespace Laravie\Warden;

use Orchestra\Support\Providers\ServiceProvider;

class WardenServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('laravie.warden', function ($app) {
            $driver = $app->make('orchestra.notifier')->driver();

            return (new Factory())->setup($driver);
        });
    }

    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        $path = realpath(__DIR__.'/../resources');

        $this->addConfigComponent('laravie/warden', 'laravie/warden', "{$path}/config");

        $this->registerObserver();
    }

    /**
     * Register auth model observers.
     *
     * @return void
     */
    protected function registerObserver()
    {
        $config = $this->app['config'];
        $model  = $config->get('laravie/warden::model', $config->get('auth.model'));

        $observer = new UserObserver(
            $this->app->make('laravie.warden'),
            $config->get('laravie/warden', [])
        );

        forward_static_call([$model, 'observe'], $observer);
    }
}
