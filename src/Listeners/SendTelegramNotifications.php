<?php

namespace Nodeloc\Telegram\Listeners;

use Flarum\Notification\Driver\NotificationDriverInterface;
use Illuminate\Contracts\Queue\Queue;
use Nodeloc\Telegram\Notifications\TelegramMailer;
use Flarum\Notification\Blueprint\BlueprintInterface;
use Flarum\User\User;
use Flarum\Notification\Event\Sending;
use Illuminate\Contracts\Events\Dispatcher;
use Flarum\User\LoginProvider;
use Flarum\Settings\SettingsRepositoryInterface;

class SendTelegramNotifications implements  NotificationDriverInterface
{
    /**
     * @var Queue
     */
    protected $queue;

    public function __construct(Queue $queue)
    {
        $this->queue = $queue;
    }

    public function subscribe($events)
    {
        $events->listen(Sending::class, [$this, 'send']);
    }

    /**
     * {@inheritDoc}
     * @throws \Exception
     */
    public function send(BlueprintInterface $blueprint, array $users): void
    {
        // The `send` method is responsible for determining any notifications need to be sent.
        // If not (for example, if there are no users to send to), there's no point in scheduling a job.
        // We HIGHLY recommend that notifications are sent via a queue job for performance reasons.
        if (count($users)) {
            $this->queue->push(new TelegramMailer($blueprint, $users));
        }
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

    public function registerType(string $blueprintClass, array $driversEnabledByDefault): void
    {
        var_dump($driversEnabledByDefault);
        app('flarum.notification.types')->addType($blueprintClass, $driversEnabledByDefault);
    }
}
