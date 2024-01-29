<?php

namespace Nodeloc\Telegram\Listeners;

use Flarum\Api\Serializer\UserSerializer;
use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\User\LoginProvider;
use Flarum\User\User;
use Illuminate\Contracts\Events\Dispatcher;

class AddUserAttributes
{
    protected $settings;
    protected $events;

    public function __construct(SettingsRepositoryInterface $settings, Dispatcher $events)
    {
        $this->settings = $settings;
        $this->events = $events;
    }

    public function __invoke(UserSerializer $serializer, User $user)
    {
        $attributes['canReceiveTelegramNotifications'] = !is_null($this->getTelegramId($user));
        $attributes['nodelocTelegramError'] = $user->flagrow_telegram_error;
        return $attributes;
    }
    /**
     * @param User $actor
     * @return int
     */
    protected function getTelegramId(User $actor)
    {
        $query = LoginProvider::where('user_id', '=', $actor->id);

        $query->where('provider', '=', 'telegram');

        $provider = $query->first();

        return optional($provider)->identifier;
    }
}
