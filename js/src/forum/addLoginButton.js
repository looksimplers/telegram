import {extend} from 'flarum/extend';
import LogInButtons from 'flarum/components/LogInButtons';

export default function () {
    extend(LogInButtons.prototype, 'items', function (items) {
        const currentDomain = window.location.origin;
        // Replace the Telegram widget script
        items.add('nodeloc-telegram',
            m('script', { async: true, src: 'https://telegram.org/js/telegram-widget.js?22',
                'data-telegram-login': 'nodeloc_bot',
                'data-size': 'large',
                'data-radius': '10',
                'data-auth-url':  currentDomain + '/auth/telegram',
                'data-request-access': 'write' })
        );
    });
}
