import {extend} from 'flarum/extend';
import User from 'flarum/models/User';
import LogInButtons from 'flarum/components/LogInButtons';
import app from 'flarum/app';

export default function () {
    extend(LogInButtons.prototype, 'items', function (items) {
        User.prototype.canReceiveTelegramNotifications = app.session.user.attribute("canReceiveTelegramNotifications");
        User.prototype.nodelocTelegramError = app.session.user.attribute("nodelocTelegramError");
        const authUrl = app.forum.attribute('baseUrl') + '/auth/telegram';
        // Replace the Telegram widget script
        items.add('nodeloc-telegram',
            m('script', { async: true, src: 'https://telegram.org/js/telegram-widget.js?22',
                'data-telegram-login': 'nodeloc_bot',
                'data-size': 'large',
                'data-radius': '10',
                'data-auth-url':  authUrl,
                'data-request-access': 'write' })
        );
    });
}
