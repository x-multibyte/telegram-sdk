# 🤖 TelegramBot

`TelegramBot` 是 SDK 的核心类，代表一个独立的 Bot 实例，封装了 Telegram Bot API 的主要能力。

## 快速上手

```php
use XBot\Telegram\Http\HttpClientConfig;
use XBot\Telegram\Http\GuzzleHttpClient;
use XBot\Telegram\TelegramBot;

$config = HttpClientConfig::fromArray([
    'token' => 'YOUR_BOT_TOKEN',
]);

$http = new GuzzleHttpClient($config);
$bot = new TelegramBot('demo', $http);
$bot->sendMessage(123456789, 'Hello');
```

## 常用方法

- `sendMessage(chatId, text, options)` 发送消息
- `getMe()` 获取 Bot 信息
- `setWebhook(url, options)` 设置 Webhook
- `getUpdates(options)` 拉取更新

更多方法请参见对应的 API 方法文档。
