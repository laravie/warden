<?php namespace Laravie\Warden\TestCase;

use Mockery as m;
use Illuminate\Container\Container;
use Laravie\Warden\WardenServiceProvider;

class WardenServiceProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        $_SERVER['laravie.warden.observer.user'] = null;
    }

    /**
     * Teardown the test environment.
     */
    public function tearDown()
    {
        unset($_SERVER['laravie.warden.observer.user']);
        m::close();
    }

    /**
     * Test Laravie\Warden\WardenServiceProvider::register() method.
     *
     * @test
     */
    public function testRegisterMethod()
    {
        $app = m::mock('\Illuminate\Contracts\Foundation\Application');
        $notifier = m::mock('\Orchestra\Notifier\NotifierManager');

        $notifier->shouldReceive('driver')->once()->andReturn(m::mock('\Orchestra\Contracts\Notification\Notification'));

        $app->shouldReceive('singleton')->once()
                ->with('laravie.warden', m::type('Closure'))
                ->andReturnUsing(function ($n, $c) use ($app) {
                    return $c($app);
                })
            ->shouldReceive('make')->once()->with('orchestra.notifier')->andReturn($notifier);

        $stub = new WardenServiceProvider($app);
        $stub->register();
    }

    /**
     * Test Laravie\Warden\WardenServiceProvider::boot() method.
     *
     * @test
     */
    public function testBootMethod()
    {
        $path = realpath(__DIR__.'/../resources/');
        $app  = new Container();

        $app['config'] = $config = m::mock('\Illuminate\Contracts\Config\Repository');
        $app['path'] = "/var/www/laravel";
        $app['laravie.warden'] = m::mock('\Laravie\Warden\Factory')->makePartial();

        $config->shouldReceive('get')->once()
                ->with('laravie/warden::model', '\Laravie\Warden\TestCase\StubUser')
                ->andReturn('\Laravie\Warden\TestCase\StubUser')
            ->shouldReceive('get')->once()
                ->with('auth.model')->andReturn('\Laravie\Warden\TestCase\StubUser')
            ->shouldReceive('get')->once()
                ->with('laravie/warden', [])
                ->andReturn(['watchlist' => ['email']]);

        $this->assertNull($_SERVER['laravie.warden.observer.user']);

        $stub = new WardenServiceProvider($app);
        $stub->boot();

        $this->assertInstanceOf('\Laravie\Warden\UserObserver', $_SERVER['laravie.warden.observer.user']);
    }
}

class StubUser
{
    public static function observe($observer)
    {
        $_SERVER['laravie.warden.observer.user'] = $observer;
    }
}
