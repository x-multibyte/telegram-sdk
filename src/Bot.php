<?php

declare(strict_types=1);

namespace XBot\Telegram;

use XBot\Telegram\Exceptions\ConfigurationException;
use XBot\Telegram\Http\GuzzleHttpClient;
use XBot\Telegram\Http\HttpClientConfig;

/**
 * Bot: minimal entrypoint for common operations.
 */
class Bot
{
    protected static ?TelegramBot $bot = null;

    /**
     * Initialize a single bot instance from configuration.
     */
    public static function init(array $config): void
    {
        $httpConfig = HttpClientConfig::fromArray($config);
        $httpClient = new GuzzleHttpClient($httpConfig);

        self::$bot = new TelegramBot($config['name'] ?? 'bot', $httpClient, $config);
    }

    /**
     * Provide an existing bot instance (e.g., from a container).
     */
    public static function useBot(TelegramBot $bot): void
    {
        self::$bot = $bot;
    }

    /**
     * Get the underlying bot or throw if not initialized.
     */
    public static function bot(): TelegramBot
    {
        if (! self::$bot) {
            throw ConfigurationException::missing('Bot not initialized. Call Bot::init($config) first.');
        }

        return self::$bot;
    }

    /**
     * Begin a message chain to a chat using the configured bot.
     */
    public static function to(int|string $chatId): BotMessage
    {
        return (new BotMessage(self::bot()))->to($chatId);
    }
}
