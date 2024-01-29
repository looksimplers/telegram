<?php

namespace Nodeloc\Telegram\Driver;

use Flarum\Notification\Driver\NotificationDriverInterface;
use Flarum\Notification\MailableInterface;
use Flarum\User\User;
use Illuminate\Contracts\Queue\Queue;
use Nodeloc\Telegram\Job\SendTelegramNotificationJob;
use Flarum\Notification\Blueprint\BlueprintInterface;
use ReflectionClass;

class TelegramNotificationDriver implements NotificationDriverInterface
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
        if (count($users)) {
            $this->queue->push(new SendTelegramNotificationJob($blueprint, $users));
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
        return $provider->identifier;
    }

    public function registerType(string $blueprintClass, array $driversEnabledByDefault): void
    {
        if ((new ReflectionClass($blueprintClass))->implementsInterface(MailableInterface::class)) {

            User::registerPreference(
                User::getNotificationPreferenceKey($blueprintClass::getType(), 'telegram'),
                'boolval',
                in_array('telegram', $driversEnabledByDefault)
            );
        }
    }
}
