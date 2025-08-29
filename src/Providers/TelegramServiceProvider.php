<?php

declare(strict_types=1);

namespace XBot\Telegram\Providers;

use Illuminate\Contracts\Container\Container;
use Illuminate\Support\ServiceProvider;
use XBot\Telegram\TelegramBot;
use XBot\Telegram\Http\GuzzleHttpClient;
use XBot\Telegram\Http\HttpClientConfig;

/**
 * Telegram SDK service provider.
 */
class TelegramServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/telegram.php', 'telegram');

        $this->app->singleton(TelegramBot::class, function (Container $app): TelegramBot {
            $config = $app['config']['telegram'];
            $httpConfig = HttpClientConfig::fromArray($config);
            $httpClient = new GuzzleHttpClient($httpConfig);

            return new TelegramBot($config['name'] ?? 'bot', $httpClient, $config);
        });

        $this->app->alias(TelegramBot::class, 'telegram');
    }

    public function boot(): void
    {
        $this->publishConfig();
        $this->registerCommands();
        $this->registerRoutes();
        $this->registerMiddleware();
    }

    protected function publishConfig(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../../config/telegram.php' => config_path('telegram.php'),
            ], 'telegram-config');
        }
    }

    protected function registerCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                \XBot\Telegram\Console\Commands\TelegramInfoCommand::class,
                \XBot\Telegram\Console\Commands\TelegramWebhookCommand::class,
                \XBot\Telegram\Console\Commands\TelegramHealthCheckCommand::class,
                \XBot\Telegram\Console\Commands\TelegramStatsCommand::class,
            ]);
        }
    }

    protected function registerRoutes(): void
    {
        if ($this->app->bound('router')) {
            $router = $this->app['router'];

            $router->group([
                'prefix' => config('telegram.webhook.route_prefix', 'telegram/webhook'),
                'middleware' => config('telegram.webhook.middleware', ['api']),
            ], function ($router) {
                $router->post('/', [\XBot\Telegram\Http\Controllers\WebhookController::class, 'handle']);
            });
        }
    }

    protected function registerMiddleware(): void
    {
        if ($this->app->bound('router')) {
            $router = $this->app['router'];
            $router->aliasMiddleware('telegram.webhook', \XBot\Telegram\Http\Middleware\VerifyWebhookSignature::class);
            $router->aliasMiddleware('telegram.rate_limit', \XBot\Telegram\Http\Middleware\TelegramRateLimit::class);
        }
    }

    public function provides(): array
    {
        return [TelegramBot::class, 'telegram'];
    }
}
