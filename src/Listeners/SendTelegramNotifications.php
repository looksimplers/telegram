<?php

namespace Nodeloc\Telegram\Listeners;

use Nodeloc\Telegram\Notifications\TelegramMailer;
use Flarum\Notification\Blueprint\BlueprintInterface;
use Flarum\User\User;
use Flarum\Notification\Event\Sending;
use Illuminate\Contracts\Events\Dispatcher;
use Flarum\User\LoginProvider;
use Flarum\Settings\SettingsRepositoryInterface;

class SendTelegramNotifications
{
    protected $settings;

    public function __construct(SettingsRepositoryInterface $settings)
    {
        $this->settings = $settings;
    }
    public function subscribe($events)
    {
        $events->listen(Sending::class, [$this, 'send']);
    }

    public function send($event)
    {
        /**
         * @var $mailer TelegramMailer
         */
        $mailer = app(TelegramMailer::class);
        // var_dump(json_encode($event));
        foreach ($event->users as $user) {
            $telegram_id = $this->shouldSendTelegramToUser($event->blueprint, $user);
            if ($telegram_id) {
                $mailer->send($event->blueprint, $user, $telegram_id);
            }
        }
        // exit(1);
    }

    protected function shouldSendTelegramToUser($blueprint, $user)
    {
        if (!$user->getPreference(User::getNotificationPreferenceKey($blueprint::getType(), 'telegram'))) {
            return false;
        }
        return $this->getTelegramId($user);
    }

    protected function getTelegramId($actor)
    {
        $provider = $actor->LoginProviders()->where('provider', '=', 'telegram')->first();
        // var_dump(json_encode($provider));
        // var_dump(json_encode($actor->username));
        return $provider->identifier;
    }
}
