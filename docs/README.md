# Telegram Bot PHP SDK

> 🤖 一个功能强大、易于使用的 PHP Telegram Bot API 封装库。

## ✨ 主要特性

- 🛡️ 类型安全的 DTO 模型
- ⚡ 高性能 HTTP 客户端
- 🔄 可配置的重试机制
- 📊 调用统计与监控
- 🏗️ Laravel 框架集成

## 🚀 快速开始

```php
use XBot\Telegram\Http\HttpClientConfig;
use XBot\Telegram\Http\GuzzleHttpClient;
use XBot\Telegram\TelegramBot;

$config = HttpClientConfig::fromArray(['token' => 'YOUR_BOT_TOKEN']);
$http = new GuzzleHttpClient($config);
$bot = new TelegramBot('demo', $http);
$bot->sendMessage(123456789, 'Hello');
```

## 📖 文档导航

- [📦 安装指南](guide/installation.md)
- [⚙️ 配置说明](guide/configuration.md)
- [📋 API 参考](api/)
- [💡 使用示例](examples/basic-usage.md)
