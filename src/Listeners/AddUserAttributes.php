<?php

namespace Nodeloc\Telegram\Listeners;

use Flarum\Api\Serializer\CurrentUserSerializer;
use Flarum\Api\Event\Serializing;
use Flarum\User\LoginProvider;
use Flarum\User\User;
use Flarum\Settings\SettingsRepositoryInterface;

class AddUserAttributes
{
    protected $settings;

    public function __construct(SettingsRepositoryInterface $settings)
    {
        $this->settings = $settings;
    }
    public function handle($event)
    {
        if ($event->isSerializer(CurrentUserSerializer::class)) {
            // $event->attributes['canReceiveTelegramNotifications'] = !is_null($event->model->attributes->flagrow_telegram_id);
            $event->attributes['canReceiveTelegramNotifications'] = !is_null($this->getTelegramId($event->model));
            $event->attributes['nodelocTelegramError'] = $event->model->flagrow_telegram_error;
        }
    }

    /**
     * @param User $actor
     * @return int
     */
    protected function getTelegramId($actor)
    {
        $query = LoginProvider::where('user_id', '=', $actor->id);

        $query->where('provider', '=', 'telegram');

        $provider = $query->first();

        return $provider->identifier;
    }
}
