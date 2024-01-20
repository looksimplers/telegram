<?php

namespace Nodeloc\Telegram;
use Nodeloc\Telegram\Controllers\TelegramAuthController;
use Nodeloc\Telegram\Controllers\TelegramBotController;
use Flarum\Extend;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\View\Factory;
use Flarum\Notification\Event\Sending;
use Flarum\Api\Event\Serializing;

return [
    (new Extend\Frontend('forum'))
        ->js(__DIR__.'/js/dist/forum.js')
        ->css(__DIR__.'/resources/less/forum.less'),
    (new Extend\Locales(__DIR__ . '/resources/locale')),
    (new Extend\Event)
        ->listen(Serializing::class, Listeners\AddUserAttributes::class)
        ->subscribe(Listeners\SendTelegramNotifications::class)
        ->subscribe(Listeners\EnableTelegramNotifications::class)
        ->subscribe(Listeners\InjectSettings::class),
    (new Extend\Frontend('admin'))
        ->js(__DIR__.'/js/dist/admin.js'),

    (new Extend\Routes('forum'))
        ->get('/auth/telegram', 'auth.telegram', TelegramAuthController::class),
];


