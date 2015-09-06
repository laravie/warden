<?php namespace Laravie\Warden;

use RuntimeException;
use Orchestra\Notifier\Message;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Support\Arrayable;
use Orchestra\Contracts\Notification\Recipient;
use Orchestra\Contracts\Notification\Notification;

class Factory
{
    /**
     * Guard status.
     *
     * @var bool
     */
    protected $unguarded = false;

    /**
     * Notifier instance.
     *
     * @var \Orchestra\Contracts\Notification\Notification
     */
    protected $mailer;

    /**
     * Notify user.
     *
     * @param  \Orchestra\Contracts\Notification\Recipient  $recipient
     * @param  \Illuminate\Database\Eloquent\Model  $user
     * @param  array  $changes
     * @param  array  $config
     *
     * @return bool
     */
    public function notify(Recipient $recipient, Model $user, array $changes, array $config = [])
    {
        if (! ($this->mailer instanceof Notification)) {
            throw new RuntimeException("Mailer need to be an instance of Orchestra\Contracts\Notification\Notification.");
        }

        if (!! $this->unguarded || empty($changes)) {
            return false;
        }

        $data = [
            'user'    => ($user instanceof Arrayable ? $user->toArray() : $user),
            'changes' => $changes,
        ];

        $message = Message::create($config['email'], $data);

        return $this->mailer->send($recipient, $message);
    }

    /**
     * Setup environment.
     *
     * @param  \Closure|string  $callback
     *
     * @return void
     */
    public function setup($callback)
    {
        $this->mailer = value($callback);
    }

    /**
     * Disable all warden observe-able.
     *
     * @return $this
     */
    public function unguard()
    {
        $this->unguarded = true;

        return $this;
    }

    /**
     * Disable all warden observe-able.
     *
     * @return $this
     */
    public function reguard()
    {
        $this->unguarded = false;

        return $this;
    }

    /**
     * Is warden guarded.
     *
     * @return bool
     */
    public function guarded()
    {
        return (! $this->unguarded);
    }
}
