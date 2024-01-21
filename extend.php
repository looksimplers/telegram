<?php

namespace Nodeloc\Telegram;
use Flarum\Api\Serializer\UserSerializer;
use Nodeloc\Telegram\Controllers\TelegramAuthController;
use Flarum\Extend;

use Nodeloc\Telegram\Listeners\AddUserAttributes;

return [
    (new Extend\Frontend('forum'))
        ->js(__DIR__.'/js/dist/forum.js')
        ->css(__DIR__.'/resources/less/forum.less'),
    (new Extend\Locales(__DIR__ . '/resources/locale')),
    (new Extend\Event)
        ->subscribe(Listeners\SendTelegramNotifications::class)
        ->subscribe(Listeners\EnableTelegramNotifications::class)
        ->subscribe(Listeners\InjectSettings::class),
    (new Extend\ApiSerializer(UserSerializer::class))->attributes(AddUserAttributes::class),
    (new Extend\Settings)
        ->serializeToForum('nodeloc-telegram.botUsername', 'nodeloc-telegram.botUsername', 'boolval')
        ->serializeToForum('nodeloc-telegram.botToken', 'nodeloc-telegram.botToken', 'boolval')
        ->serializeToForum('nodeloc-telegram.enableNotifications', 'nodeloc-telegram.enableNotifications', 'boolval'),
    (new Extend\Frontend('admin'))
        ->js(__DIR__.'/js/dist/admin.js'),
    (new Extend\Routes('forum'))
        ->get('/auth/telegram', 'auth.telegram', TelegramAuthController::class),
];


