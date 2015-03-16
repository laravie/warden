<?php namespace Orchestra\Warden\TestCase;

use Mockery as m;
use Illuminate\Container\Container;
use Orchestra\Warden\WardenServiceProvider;

class WardenServiceProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        $_SERVER['orchestra.warden.observer.user'] = null;
    }

    /**
     * Teardown the test environment.
     */
    public function tearDown()
    {
        unset($_SERVER['orchestra.warden.observer.user']);
        m::close();
    }

    /**
     * Test Orchestra\Warden\WardenServiceProvider::register() method.
     *
     * @test
     */
    public function testRegisterMethod()
    {
        $app = m::mock('\Illuminate\Contracts\Container\Container', '\ArrayAccess');
        $notifier = m::mock('\Orchestra\Notifier\NotifierManager');

        $notifier->shouldReceive('driver')->once()->andReturn(m::mock('\Orchestra\Contracts\Notification\Notification'));

        $app->shouldReceive('singleton')->once()
                ->with('orchestra.warden', m::type('Closure'))
                ->andReturnUsing(function ($n, $c) use ($app) {
                    return $c($app);
                })
            ->shouldReceive('offsetGet')->once()->with('orchestra.notifier')->andReturn($notifier);

        $stub = new WardenServiceProvider($app);
        $stub->register();
    }

    /**
     * Test Orchestra\Warden\WardenServiceProvider::boot() method.
     *
     * @test
     */
    public function testBootMethod()
    {
        $path = realpath(__DIR__.'/../resources/');
        $app  = new Container();

        $app['config'] = $config = m::mock('\Illuminate\Contracts\Config\Repository');
        $app['path'] = "/var";
        $app['orchestra/warden'] = m::mock('\Orchestra\Warden\Factory')->makePartial();

        $config->shouldReceive('get')->once()
                ->with('orchestra/warden::model', '\Orchestra\Warden\TestCase\StubUser')
                ->andReturn('\Orchestra\Warden\TestCase\StubUser')
            ->shouldReceive('get')->once()
                ->with('auth.model')->andReturn('\Orchestra\Warden\TestCase\StubUser')
            ->shouldReceive('get')->once()
                ->with('orchestra/warden', [])
                ->andReturn(['watchlist' => ['email']]);

        $this->assertNull($_SERVER['orchestra.warden.observer.user']);

        $stub = new WardenServiceProvider($app);
        $stub->boot();

        $this->assertInstanceOf('\Orchestra\Warden\UserObserver', $_SERVER['orchestra.warden.observer.user']);
    }
}

class StubUser
{
    public static function observe($observer)
    {
        $_SERVER['orchestra.warden.observer.user'] = $observer;
    }
}
