# 🔰 基础使用

```php
require 'vendor/autoload.php';

use XBot\Telegram\Http\HttpClientConfig;
use XBot\Telegram\Http\GuzzleHttpClient;
use XBot\Telegram\TelegramBot;

$config = HttpClientConfig::fromArray([
    'token' => 'YOUR_BOT_TOKEN',
]);

$http = new GuzzleHttpClient($config);
$bot = new TelegramBot('my-bot', $http);
$bot->sendMessage(123456789, 'Hello, World!');
```

## 处理更新

```php
use XBot\Telegram\Models\DTO\Update;

$update = Update::fromArray($webhookData);
if ($update->isMessage()) {
    $bot->sendMessage($update->message->chat->id, '你好！');
}
```
