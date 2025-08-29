# Telegram Bot PHP SDK

一个功能强大、易于使用的 PHP Telegram Bot API SDK，支持多 Token、多 Bot、多实例管理，实例间完全隔离互不干扰。

## ✨ 特性

- 🤖 **多 Bot 支持** - 支持同时管理多个 Bot 实例
- 🔒 **实例隔离** - 每个 Bot 实例完全独立，互不干扰
- ⚡ **高性能** - 基于 Guzzle HTTP 客户端，支持连接池和异步请求
- 🎯 **类型安全** - 完整的 DTO 模型和类型提示
- 🛡️ **异常处理** - 完善的异常处理体系
- 🔄 **重试机制** - 内置智能重试和错误恢复
- 📊 **统计监控** - 详细的调用统计和性能监控
- 🌐 **可选 Laravel Bridge** - 单独安装以获取 Laravel 集成
- 🎨 **链式调用** - 优雅的 API 设计
- 📝 **完整文档** - 详细的使用文档和示例

## 📦 安装

使用 Composer 安装：

```bash
composer require xbot-my/telegram-sdk
```

### Laravel 集成（可选）

要在 Laravel 项目中使用本 SDK，请额外安装桥接包并发布配置：

```bash
composer require xbot-my/telegram-sdk-laravel-bridge
php artisan vendor:publish --provider="XBot\Telegram\Providers\TelegramServiceProvider"
```

## 🚀 快速开始

### 一行快速发送（Bot 入口）

```php
use XBot\Telegram\Bot;

Bot::init([
    'default' => 'main',
    'bots' => [
        'main' => ['token' => 'YOUR_BOT_TOKEN']
    ],
]);

Bot::to(123456789)->html()->message('<b>Hello</b>');
// 指定 Bot
Bot::via('marketing')->to(123456789)->message('Hi');
```

### 基础使用

```php
use XBot\Telegram\BotManager;
use XBot\Telegram\Http\HttpClientConfig;
use XBot\Telegram\Http\GuzzleHttpClient;
use XBot\Telegram\TelegramBot;

// 创建单个 Bot 实例
$config = HttpClientConfig::fromArray([
    'token' => 'YOUR_BOT_TOKEN',
    'timeout' => 30,
]);

$httpClient = new GuzzleHttpClient($config);
$bot = new TelegramBot('my-bot', $httpClient);

// 发送消息
$message = $bot->sendMessage(
    chatId: 12345,
    text: 'Hello, World!'
);

echo "Message sent! ID: {$message->messageId}";
```

### 多 Bot 管理

```php
use XBot\Telegram\BotManager;

// 配置多个 Bot
$config = [
    'default' => 'main',
    'bots' => [
        'main' => [
            'token' => 'MAIN_BOT_TOKEN',
            'timeout' => 30,
        ],
        'customer-service' => [
            'token' => 'CS_BOT_TOKEN',
            'timeout' => 15,
        ],
        'marketing' => [
            'token' => 'MARKETING_BOT_TOKEN',
            'timeout' => 60,
        ],
    ],
];

$manager = new BotManager($config);

// 使用默认 Bot
$mainBot = $manager->bot();
$mainBot->sendMessage(12345, 'Main bot message');

// 使用指定 Bot
$csBot = $manager->bot('customer-service');
$csBot->sendMessage(12345, 'Customer service reply');

$marketingBot = $manager->bot('marketing');
$marketingBot->sendMessage(12345, 'Marketing campaign');
```

## 🧩 Webhook 部署与排错
- 部署
  - 路由：`POST /{prefix}/{botName}`，默认前缀 `telegram/webhook`。
  - 注册：`setWebhook('https://your.app/telegram/webhook/main', ['secret_token' => '...'])`。
- 常见问题
  - 非 HTTPS：Telegram 要求 HTTPS，HTTP 会被拒绝。
  - 403/签名失败：确保 `secret_token` 与服务端验证一致（`telegram.webhook` 中间件）。
  - 429：降低速率或设置 `max_connections`，并考虑 `deleteWebhook(true)` 清理积压。
  - 内网不可达：对外需可访问，必要时通过反向代理或隧道（如 Cloudflare Tunnel）。
