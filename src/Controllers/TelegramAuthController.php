<?php

namespace Nodeloc\Telegram\Controllers;

use Flarum\Forum\Auth\Registration;
use Flarum\Forum\Auth\ResponseFactory;
use Flarum\Http\UrlGenerator;
use Flarum\Settings\SettingsRepositoryInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Exception;
use Flarum\Http\Response as FlarumResponse;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Client;
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
            throw new Exception('No bot token configured for Telegram');
        }

        $this->client = new Client([
            'base_uri' => 'https://api.telegram.org/bot' . $token . '/',
        ]);
    }

    public function handle(Request $request): ResponseInterface
    {
        $provider = 'telegram';
        $auth = $request->getQueryParams();

        try {
            $auth = checkTelegramAuthorization($_GET);

            $user = $request->getAttribute('actor');
            if ($user && $user->id) {
                $identifier = array_get($auth, 'id');
                // var_dump($this->checkTelegramId($identifier));exit(1);
                if ($this->checkTelegramId($provider, $identifier)) {
                    $content = '<div style="text-align:center;font-family:Arial;">You can\'t link this telegram account to this user.</div>';
                    return new HtmlResponse($content);
                }
                $user->loginProviders()->create(compact('provider', 'identifier'));
                $content = '<script>window.close();window.opener.document.location.reload(true);</script>';

                return new HtmlResponse($content);
            }

            $suggestions = [];
            if (array_get($auth, 'username')) $suggestions['username'] = array_get($auth, 'username');
            if (array_get($auth, 'photo_url')) $suggestions['avatarUrl'] = array_get($auth, 'photo_url');


            return $this->authResponse->make(
                $provider, array_get($auth, 'id'),
                function (Registration $registration) use ($suggestions) {
                    if ($suggestions['username']) {
                        $registration->suggestUsername($suggestions['username']);
                    }
                    $registration->setPayload($suggestions);
                }
            );
        } catch (Exception $e) {
            die ($e->getMessage());
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
            throw new Exception('Data is NOT from Telegram');
        }
        if ((time() - $auth_data['auth_date']) > 86400) {
            throw new Exception('Data is outdated');
        }
        return $auth_data;
    }
}
