<?php

declare(strict_types=1);

namespace XBot\Telegram\Facades;

use Illuminate\Support\Facades\Facade;
use XBot\Telegram\TelegramBot;

/**
 * @method static \XBot\Telegram\Models\DTO\User getMe()
 * @method static \XBot\Telegram\Models\DTO\Message sendMessage(int|string $chatId, string $text, array $options = [])
 * @method static \XBot\Telegram\Models\DTO\Message editMessageText(int|string $chatId, int $messageId, string $text, array $options = [])
 * @method static bool deleteMessage(int|string $chatId, int $messageId)
 * @method static \XBot\Telegram\Models\DTO\Message forwardMessage(int|string $chatId, int|string $fromChatId, int $messageId, array $options = [])
 * @method static int copyMessage(int|string $chatId, int|string $fromChatId, int $messageId, array $options = [])
 * @method static array getUpdates(array $options = [])
 * @method static bool setWebhook(string $url, array $options = [])
 * @method static bool deleteWebhook(bool $dropPendingUpdates = false)
 * @method static array getWebhookInfo()
 * @method static \XBot\Telegram\Models\DTO\Message sendPhoto(int|string $chatId, string $photo, array $options = [])
 * @method static \XBot\Telegram\Models\DTO\Message sendVideo(int|string $chatId, string $video, array $options = [])
 * @method static \XBot\Telegram\Models\DTO\Message sendAudio(int|string $chatId, string $audio, array $options = [])
 * @method static \XBot\Telegram\Models\DTO\Message sendDocument(int|string $chatId, string $document, array $options = [])
 * @method static \XBot\Telegram\Models\DTO\Message sendSticker(int|string $chatId, string $sticker, array $options = [])
 * @method static \XBot\Telegram\Models\DTO\Message sendAnimation(int|string $chatId, string $animation, array $options = [])
 * @method static \XBot\Telegram\Models\DTO\Message sendVoice(int|string $chatId, string $voice, array $options = [])
 * @method static \XBot\Telegram\Models\DTO\Message sendLocation(int|string $chatId, float $latitude, float $longitude, array $options = [])
 * @method static \XBot\Telegram\Models\DTO\Message sendContact(int|string $chatId, string $phoneNumber, string $firstName, array $options = [])
 * @method static \XBot\Telegram\Models\DTO\Message sendPoll(int|string $chatId, string $question, array $options, array $settings = [])
 * @method static array getChat(int|string $chatId)
 * @method static array getChatMember(int|string $chatId, int $userId)
 * @method static int getChatMemberCount(int|string $chatId)
 * @method static bool banChatMember(int|string $chatId, int $userId, array $options = [])
 * @method static bool unbanChatMember(int|string $chatId, int $userId, array $options = [])
 * @method static bool restrictChatMember(int|string $chatId, int $userId, array $permissions, array $options = [])
 * @method static bool promoteChatMember(int|string $chatId, int $userId, array $options = [])
 * @method static bool answerCallbackQuery(string $callbackQueryId, array $options = [])
 * @method static bool answerInlineQuery(string $inlineQueryId, array $results, array $options = [])
 * @method static \XBot\Telegram\Models\Response\TelegramResponse call(string $method, array $parameters = [])
 * @method static bool healthCheck()
 * @method static array getStats()
 *
 * @see \XBot\Telegram\TelegramBot
 */
class Telegram extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'telegram';
    }

    public static function to(int|string $chatId): TelegramMessageBuilder
    {
        /** @var TelegramBot $bot */
        $bot = static::getFacadeRoot();
        return new TelegramMessageBuilder($bot, $chatId);
    }
}

class TelegramMessageBuilder
{
    protected TelegramBot $bot;
    protected int|string $chatId;
    protected array $options = [];

    public function __construct(TelegramBot $bot, int|string $chatId)
    {
        $this->bot = $bot;
        $this->chatId = $chatId;
    }

    public function options(array $options): static
    {
        $this->options = array_merge($this->options, $options);
        return $this;
    }

    public function replyMarkup(array $markup): static
    {
        $this->options['reply_markup'] = $markup;
        return $this;
    }

    public function keyboard(array $keyboard): static
    {
        $this->options['reply_markup'] = ['inline_keyboard' => $keyboard];
        return $this;
    }

    public function parseMode(string $mode): static
    {
        $this->options['parse_mode'] = $mode;
        return $this;
    }

    public function html(): static
    {
        return $this->parseMode('HTML');
    }

    public function markdown(): static
    {
        return $this->parseMode('Markdown');
    }

    public function silent(): static
    {
        $this->options['disable_notification'] = true;
        return $this;
    }

    public function protect(): static
    {
        $this->options['protect_content'] = true;
        return $this;
    }

    public function replyTo(int $messageId): static
    {
        $this->options['reply_to_message_id'] = $messageId;
        return $this;
    }

    public function message(string $text): \XBot\Telegram\Models\DTO\Message
    {
        return $this->bot->sendMessage($this->chatId, $text, $this->options);
    }
}
