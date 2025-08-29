# 📦 安装指南

## 环境要求

- PHP >= 8.3
- 扩展: `json`, `curl`, `mbstring`, `openssl`
- Composer

## 使用 Composer 安装

```bash
composer require xbot-my/telegram-sdk
```

## 验证安装

```php
require 'vendor/autoload.php';

use XBot\Telegram\TelegramBot;
use XBot\Telegram\Http\GuzzleHttpClient;
use XBot\Telegram\Http\HttpClientConfig;

$config = HttpClientConfig::fromArray(['token' => '123456789:TEST']);
$bot = new TelegramBot('test', new GuzzleHttpClient($config));

echo "Bot name: {$bot->getName()}\n";
```

## Laravel 项目

发布配置文件：

```bash
php artisan vendor:publish --provider="XBot\\Telegram\\Providers\\TelegramServiceProvider"
```
