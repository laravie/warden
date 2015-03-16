<?php namespace Orchestra\Warden;

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
        $this->app->singleton('orchestra.warden', function ($app) {
            $driver = $app['orchestra.notifier']->driver();

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

        $this->addConfigComponent('orchestra/warden', 'orchestra/warden', $path.'/config');

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
        $model  = $config->get('orchestra/warden::model', $config->get('auth.model'));

        $observer = new UserObserver(
            $this->app['orchestra/warden'],
            $config->get('orchestra/warden', [])
        );

        forward_static_call([$model, 'observe'], $observer);
    }
}
