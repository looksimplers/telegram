<?php

namespace Nodeloc\Telegram\Controllers;

use Flarum\Forum\Auth\Registration;
use Flarum\Forum\Auth\ResponseFactory;
use Flarum\Http\UrlGenerator;
use Flarum\Settings\SettingsRepositoryInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Exception;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Laminas\Diactoros\Response\HtmlResponse;
use Flarum\User\LoginProvider;

class TelegramAuthController implements RequestHandlerInterface
{
    protected $authResponse;
    protected $settings;
    protected $url;
    protected $client;

    public function __construct(ResponseFactory $authResponse, SettingsRepositoryInterface $settings, UrlGenerator $url)
    {
        $this->authResponse = $authResponse;
        $this->settings = $settings;
        $this->url = $url;

        $token = $settings->get('nodeloc-telegram.botToken');

        if (!$token) {
            throw new Exception('No bot token configured for TelegramProvide');
        }
    }

    public function handle(Request $request): ResponseInterface
    {
        $provider = 'telegram';

        try {
            $auth = $this->checkTelegramAuthorization($_GET);
            $user = $request->getAttribute('actor');

            if ($user && $user->id) {
                $identifier = $auth['id'] ?? null;

                if ($this->checkTelegramId($provider, $identifier)) {
                    $content = '<div style="text-align:center;font-family:Arial;">You can\'t link this telegram account to this user.</div>';
                    return new HtmlResponse($content);
                }

                $user->loginProviders()->create(compact('provider', 'identifier'));
                $content = '<script>window.opener.document.location.reload(true);</script>';

                return new HtmlResponse($content);
            }

            $suggestions = [];
            if ($auth['username']) $suggestions['username'] = $auth['username'];
            if ($auth['photo_url']) $suggestions['avatar_url'] = $auth['photo_url'];


            return $this->authResponse->make(
                $provider, $auth['id'], function (Registration $registration) use ($suggestions) {
                    // 设置 TelegramProvide 提供的信息
                    $registration->provide('username', $suggestions['username']);
                    $registration->provide('avatar_url', $suggestions['avatar_url']);
                    $registration->setPayload($suggestions);
            }
            );
        } catch (Exception $e) {
            // 在异常情况下返回错误响应
            return new HtmlResponse('Error: ' . $e->getMessage(), 500);
        }
    }


    protected function checkTelegramId($provider, $identifier)
    {
        $provider = LoginProvider::where(compact('provider', 'identifier'))->first();
        return $provider;
    }

    function checkTelegramAuthorization($auth_data) {
        $check_hash = $auth_data['hash'];
        unset($auth_data['hash']);
        $data_check_arr = [];
        foreach ($auth_data as $key => $value) {
            $data_check_arr[] = $key . '=' . $value;
        }
        sort($data_check_arr);
        $data_check_string = implode("\n", $data_check_arr);
        $secret_key = hash('sha256', $this->settings->get('nodeloc-telegram.botToken'), true);
        $hash = hash_hmac('sha256', $data_check_string, $secret_key);
        if (strcmp($hash, $check_hash) !== 0) {
            throw new Exception('Data is NOT from TelegramProvide');
        }
        if ((time() - $auth_data['auth_date']) > 86400) {
            throw new Exception('Data is outdated');
        }
        return $auth_data;
    }
}
