# Telegram Bot PHP SDK

A powerful yet easy to use PHP SDK for the Telegram Bot API.

## ✨ Features

- ⚡ **High performance** HTTP client
- 🎯 **Type safe** DTO models
- 🛡️ **Robust** exception handling
- 🔄 **Retry mechanism** with configurable attempts
- 🌐 **Laravel integration** out of the box
- 🎨 **Fluent** message builder API
- 📝 **Comprehensive** documentation

## 📦 Installation

```bash
composer require xbot-my/telegram-sdk
```

### Laravel Integration

Publish the configuration file:

```bash
php artisan vendor:publish --provider="XBot\\Telegram\\Providers\\TelegramServiceProvider"
```

## 🚀 Quick Start

```php
use XBot\Telegram\Http\HttpClientConfig;
use XBot\Telegram\Http\GuzzleHttpClient;
use XBot\Telegram\TelegramBot;

$config = HttpClientConfig::fromArray([
    'token' => 'YOUR_BOT_TOKEN',
]);

$httpClient = new GuzzleHttpClient($config);
$bot = new TelegramBot('my-bot', $httpClient);

$bot->sendMessage(123456789, 'Hello, World!');
```

## 📋 Configuration

The published configuration file `config/telegram.php` contains the bot token and HTTP client settings:

```php
return [
    'name' => env('TELEGRAM_BOT_NAME', 'bot'),
    'token' => env('TELEGRAM_BOT_TOKEN'),
    'base_url' => env('TELEGRAM_BASE_URL', 'https://api.telegram.org/bot'),
    'timeout' => (int) env('TELEGRAM_TIMEOUT', 30),
];
```

## 🤝 Contributing

Contributions are welcome! Please see the [contribution guide](CONTRIBUTING.md).

## 📄 License

The project is open-sourced under the [MIT license](LICENSE).
