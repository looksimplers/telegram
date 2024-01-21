<?php

namespace Nodeloc\Telegram\Listeners;

use Flarum\Api\Serializer\ForumSerializer;
use Flarum\Api\Event\Serializing;
use Flarum\Settings\SettingsRepositoryInterface;

class InjectSettings
{
    protected $settings;

    public function __construct(SettingsRepositoryInterface $settings)
    {
        $this->settings = $settings;
    }

    public function subscribe($events)
    {
        $events->listen(Serializing::class, [$this, 'settings']);
    }

    public function settings(Serializing $event)
    {
        if ($event->serializer instanceof ForumSerializer) {
            $event->attributes['nodeloc-telegram.enableNotifications'] = (bool)$this->settings->get('nodeloc-telegram.enableNotifications');
            $event->attributes['nodeloc-telegram.botUsername'] = $this->settings->get('nodeloc-telegram.botUsername');
        }
    }
}
