<?php namespace Laravie\Warden\TestCase;

use Mockery as m;
use Laravie\Warden\UserObserver;

class UserObserverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Teardown the test environment.
     */
    public function tearDown()
    {
        m::close();
    }

    /**
     * Test Laravie\Warden\UserObserver::updating() method.
     *
     * @test
     */
    public function testUpdatingMethod()
    {
        $recipient = m::type('\Orchestra\Contracts\Notification\Recipient');
        $factory = m::mock('\Laravie\Warden\Factory');
        $model = m::mock('\Orchestra\Model\User');

        $config = ['watchlist' => 'email'];
        $changes = ['email' => 'foo@orchestraplatform.com'];

        $model->shouldReceive('isDirty')->once()->with('email')->andReturn(true)
            ->shouldReceive('getOriginal')->once()->with('email')->andReturn('hello@orchestraplatform.com')
            ->shouldReceive('getRecipientName')->once()->andReturn('Administrator')
            ->shouldReceive('getAttribute')->once()->with('email')->andReturn('foo@orchestraplatform.com');

        $factory->shouldReceive('notify')->once()->with($recipient, $model, $changes, $config)->andReturn(true);

        $stub = new UserObserver($factory, $config);
        $this->assertNull($stub->updating($model));
    }
}
