# ⚙️ 配置说明

默认配置文件位于 `config/telegram.php`：

```php
return [
    'name' => env('TELEGRAM_BOT_NAME', 'bot'),
    'token' => env('TELEGRAM_BOT_TOKEN'),
    'base_url' => env('TELEGRAM_BASE_URL', 'https://api.telegram.org/bot'),
    'timeout' => (int) env('TELEGRAM_TIMEOUT', 30),
    'retry_attempts' => (int) env('TELEGRAM_RETRY_ATTEMPTS', 3),
    'retry_delay' => (int) env('TELEGRAM_RETRY_DELAY', 1000),
    'verify_ssl' => env('TELEGRAM_VERIFY_SSL', true),
    'token_validation' => [
        'enabled' => env('TELEGRAM_VALIDATE_TOKEN', true),
        'pattern' => env('TELEGRAM_TOKEN_PATTERN', '^\d+:[A-Za-z0-9_-]{32,}$'),
    ],
    'webhook' => [
        'route_prefix' => 'telegram/webhook',
        'middleware' => ['api', 'telegram.webhook'],
    ],
    'logging' => [
        'enabled' => env('TELEGRAM_LOGGING_ENABLED', true),
        'level' => env('TELEGRAM_LOG_LEVEL', 'info'),
    ],
];
```

将所需的值添加到 `.env` 文件即可配置 Bot。
