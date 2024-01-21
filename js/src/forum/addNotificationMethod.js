import {extend} from 'flarum/extend';
import app from 'flarum/app';
import NotificationGrid from 'flarum/components/NotificationGrid';
import SettingsPage from 'flarum/components/SettingsPage';
import LogInButton from 'flarum/components/LogInButton';

export default function () {
    extend(NotificationGrid.prototype, 'notificationMethods', function (items) {
        if (!app.forum.attribute('nodeloc-telegram.enableNotifications')) {
            return;
        }
        let user = app.session.user;
        if (!user || !user.canReceiveTelegramNotifications()) {
            return;
        }
        // Add telegram notifications method column
        items.add('telegram', {
            name: 'telegram',
            icon: 'fab fa-telegram-plane',
            label: app.translator.trans('nodeloc-telegram.forum.settings.notify_by_telegram_heading'),
        });
    });
    extend(SettingsPage.prototype, 'accountItems', function (items) {
        if (!app.forum.attribute('nodeloc-telegram.enableNotifications')) {
            return;
        }

        let user = app.session.user;
        if (user && !user.canReceiveTelegramNotifications()) {
            // add button to link current account with telegram
            items.add('nodeloc-telegram',
                <div>
                    <LogInButton
                        className="Button LogInButton--telegram"
                        style="display:inline-block; width:auto"
                        icon="fab fa-telegram-plane"
                        path="/auth/telegram">
                        {app.translator.trans('nodeloc-telegram.forum.link_telegram_button')}
                    </LogInButton>
                </div>
            );
        }
    });
    extend(SettingsPage.prototype, 'notificationsItems', function (items) {

        if (!app.forum.attribute('nodeloc-telegram.enableNotifications')) {
            return;
        }
        let user = app.session.user;
        if (!user || !user.nodelocTelegramError()) {
            return;
        }
        const botUsername = app.forum.attribute('nodeloc-telegram.botUsername');

        items.add('nodelocTelegramError', {
            view() {
                return m('.Alert', m('p', app.translator.trans('nodeloc-telegram.forum.settings.unblock_telegram_bot', {
                    a: m('a', {href: 'https://t.me/' + botUsername}),
                    username: '@' + botUsername,
                })))
            },
        });
    });
}


