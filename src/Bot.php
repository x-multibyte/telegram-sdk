<?php

declare(strict_types=1);

namespace XBot\Telegram;

use XBot\Telegram\Http\GuzzleHttpClient;
use XBot\Telegram\Http\HttpClientConfig;

/**
 * Bot: minimal entrypoint for creating `TelegramBot` instances.
 *
 * Example:
 * `$bot = Bot::token('TOKEN');`
 * `$bot->getMe();`
 */
class Bot
{
    /**
     * Create a `TelegramBot` from a raw token.
     *
     * @param string $token   Bot token.
     * @param array  $options Optional configuration for the HTTP client.
     */
    public static function token(string $token, array $options = []): TelegramBot
    {
        $name = $options['name'] ?? 'bot';
        $configArray = ['token' => $token] + $options;

        $httpConfig = HttpClientConfig::fromArray($configArray, $name);
        $httpClient = new GuzzleHttpClient($httpConfig);

        return new TelegramBot($name, $httpClient, $configArray);
    }
}

