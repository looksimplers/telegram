# Telegram login and notifications by Nodeloc (rewritten original extension of [Flagrow](https://discuss.flarum.org/d/1832-flagrow-extension-developer-group))

[![MIT license](https://img.shields.io/badge/license-MIT-blue.svg)](https://github.com/nodeloc/telegram/blob/master/LICENSE.md) [![Latest Stable Version](https://img.shields.io/packagist/v/nodeloc/telegram.svg)](https://packagist.org/packages/nodeloc/telegram) [![Total Downloads](https://img.shields.io/packagist/dt/nodeloc/telegram.svg)](https://packagist.org/packages/nodeloc/telegram)

This extension adds a "Log in with Telegram" button that uses the new [Telegram Login widget](https://telegram.org/blog/login) and add an option to receive notifications via Telegram as well.

## Installation

```bash
composer require nodeloc/telegram
```

## Updating

```bash
composer update nodeloc/telegram
php flarum migrate
php flarum cache:clear
```

## Configuration

Follow [Telegram instructions](https://core.telegram.org/widgets/login#setting-up-a-bot) to create a bot for the login widget.

Then copy the **Bot Username** and **Bot Token** to the extension settings. The username and token must belong to the same bot.

If you check **Enable Notifications**, the login widget will ask for permission for the bot to message the user and an additional Telegram column will appear in the user's notification settings.

## Support our work

We prefer to keep our work available to everyone.

## Security

If you discover a security vulnerability within Telegram login and notifications, please send an email to security@spitsyn.net. All security vulnerabilities will be promptly addressed.

Please include as many details as possible. You can use `php flarum info` to get the PHP, Flarum and extension versions installed.

## Links

- [Flarum Discuss post](https://www.nodeloc.com)
- [Source code on GitHub](https://github.com/nodeloc/telegram)
- [Report an issue](https://github.com/nodeloc/telegram/issues)
- [Download via Packagist](https://packagist.org/packages/nodeloc/telegram)

