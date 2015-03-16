<?php namespace Orchestra\Warden;

use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Model;
use Orchestra\Notifier\GenericRecipient;

class UserObserver
{
    /**
     * Configuration.
     *
     * @var array
     */
    protected $config = [];

    /**
     * Factory instance.
     *
     * @var Factory
     */
    protected $factory;

    /**
     * Construct a new observer instance.
     *
     * @param  \Orchestra\Warden\Factory $factory
     * @param  array  $config
     */
    public function __construct(Factory $factory, array $config = [])
    {
        $this->config  = $config;
        $this->factory = $factory;
    }

    /**
     * Observe model saving for User.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     *
     * @return void
     */
    public function updating(Model $model)
    {
        $changes   = [];
        $watchlist = (array) Arr::get($this->config, 'watchlist', []);

        foreach ($watchlist as $attribute) {
            if ($model->isDirty($attribute)) {
                $changes[$attribute] = $model->getAttribute($attribute);
            }
        }

        $recipient = new GenericRecipient($model->getOriginal('email'), $model->getRecipientName());

        $this->factory->notify($recipient, $model, $changes, $this->config);
    }
}
