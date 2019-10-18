import app from 'flarum/app';

import TelegramSettingsModal from './components/TelegramSettingsModal';

app.initializers.add('dexif-telegram', () => {
  app.extensionSettings['dexif-telegram'] = () => app.modal.show(new TelegramSettingsModal());
});
