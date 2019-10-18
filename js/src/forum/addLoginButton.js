import {extend} from 'flarum/extend';
import app from 'flarum/app';
import LogInButtons from 'flarum/components/LogInButtons';
import LogInButton from 'flarum/components/LogInButton';

export default function () {
    extend(LogInButtons.prototype, 'items', function (items) {
        items.add('dexif-telegram',
            <LogInButton
                className="Button LogInButton--telegram"
                icon="fab fa-telegram-plane"
                path="/auth/telegram">
                {app.translator.trans('dexif-telegram.forum.log_in_with_telegram_button')}
            </LogInButton>
        );
    });
}
