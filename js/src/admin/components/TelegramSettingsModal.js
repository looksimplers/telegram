import SettingsModal from 'flarum/components/SettingsModal';
import Switch from 'flarum/components/Switch';

export default class TelegramSettingsModal extends SettingsModal {
  className() {
    return 'TelegramSettingsModal Modal--small';
  }

  title() {
    return app.translator.trans('dexif-telegram.admin.settings.title');
  }

  form() {
    return [
      <div className="Form-group">
        <label>{app.translator.trans('dexif-telegram.admin.settings.field.botUsername')}</label>
        <input className="FormControl" bidi={this.setting('dexif-telegram.botUsername')} placeholder="SampleBot" />
      </div>,
      <div className="Form-group">
        <label>{app.translator.trans('dexif-telegram.admin.settings.field.botToken')}</label>
        <input className="FormControl" bidi={this.setting('dexif-telegram.botToken')} placeholder="123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11" />
      </div>,
      <div className="Form-group" style="min-height:50px">
        <label>{app.translator.trans('dexif-telegram.admin.settings.field.enableNotifications')}</label>
            <Switch
                state={[true, '1'].indexOf(this.setting('dexif-telegram.enableNotifications')()) !== -1}
                onchange={this.setting('dexif-telegram.enableNotifications')}
                children={app.translator.trans('dexif-telegram.admin.settings.field.enableNotifications')} />
      </div>
    ];
  }
}


