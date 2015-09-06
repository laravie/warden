<?php namespace Laravie\Warden\TestCase;

use Mockery as m;
use Laravie\Warden\Factory;

class FactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Teardown the test environment.
     */
    public function tearDown()
    {
        m::close();
    }

    /**
     * Test Laravie\Warden\Factory::notify() method.
     *
     * @test
     */
    public function testNotifyMethod()
    {
        $recipient = m::mock('\Orchestra\Contracts\Notification\Recipient');
        $user = m::mock('\Orchestra\Model\User');
        $mailer = m::mock('\Orchestra\Contracts\Notification\Notification');

        $user->shouldReceive('toArray')->once()->andReturn(['email' => 'foo@orchestraplatform.com']);
        $mailer->shouldReceive('send')->once()->with($recipient, m::type('\Orchestra\Notifier\Message'))->andReturn(true);

        $stub = new Factory();
        $stub->setup($mailer);

        $this->assertTrue(
            $stub->notify(
                $recipient,
                $user,
                ['email' => 'foo@orchestraplatform.com'],
                ['email' => 'changes']
            )
        );
    }

    /**
     * Test Laravie\Warden\Factory::notify() method.
     *
     * @test
     */
    public function testNotifyMethodWhenUnguarded()
    {
        $recipient = m::mock('\Orchestra\Contracts\Notification\Recipient');
        $user = m::mock('\Orchestra\Model\User');
        $mailer = m::mock('\Orchestra\Contracts\Notification\Notification');

        $stub = new Factory();
        $stub->unguard();
        $stub->setup($mailer);

        $this->assertFalse(
            $stub->notify(
                $recipient,
                $user,
                ['email' => 'foo@orchestraplatform.com'],
                ['email' => 'changes']
            )
        );
    }

    /**
     * Test Laravie\Warden\Factory::notify() method throws an exception.
     *
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Mailer need to be an instance of Orchestra\Contracts\Notification\Notification.
     */
    public function testNotifyMethodThrowsException()
    {
        $recipient = m::mock('\Orchestra\Contracts\Notification\Recipient');
        $user = m::mock('\Orchestra\Model\User');

        $stub = new Factory();
        $stub->notify(
            $recipient,
            $user,
            ['email' => 'foo@orchestraplatform.com'],
            []
        );
    }

    /**
     * Test Laravie\Warden\Factory::unguard() and
     * Laravie\Warden\Factory::reguard() method.
     *
     * @test
     */
    public function testGuardsMethod()
    {
        $stub = new Factory();

        $refl = new \ReflectionObject($stub);
        $unguarded = $refl->getProperty('unguarded');
        $unguarded->setAccessible(true);

        $this->assertFalse($unguarded->getValue($stub));

        $stub->unguard();

        $this->assertFalse($stub->guarded());
        $this->assertTrue($unguarded->getValue($stub));

        $stub->reguard();

        $this->assertTrue($stub->guarded());
        $this->assertFalse($unguarded->getValue($stub));
    }
}
