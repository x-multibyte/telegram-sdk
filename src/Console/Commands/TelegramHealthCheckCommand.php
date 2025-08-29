<?php

declare(strict_types=1);

namespace XBot\Telegram\Console\Commands;

use Illuminate\Console\Command;
use XBot\Telegram\TelegramBot;

/**
 * Telegram bot health check command.
 */
class TelegramHealthCheckCommand extends Command
{
    protected $signature = 'telegram:health {--json} {--timeout=10 : Health check timeout in seconds}';

    protected $description = 'Check Telegram bot health status';

    protected TelegramBot $bot;

    public function __construct(TelegramBot $bot)
    {
        parent::__construct();
        $this->bot = $bot;
    }

    public function handle(): int
    {
        $result = $this->checkBot();

        if ($this->option('json')) {
            $this->info(json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        } else {
            $this->displayResult($result);
        }

        return $result['is_healthy'] ? 0 : 1;
    }

    protected function checkBot(): array
    {
        $startTime = microtime(true);

        try {
            $isHealthy = $this->bot->healthCheck();
            $responseTime = microtime(true) - $startTime;

            return [
                'name' => $this->bot->getName(),
                'is_healthy' => $isHealthy,
                'response_time' => round($responseTime * 1000, 2),
                'error' => null,
            ];
        } catch (\Throwable $e) {
            $responseTime = microtime(true) - $startTime;

            return [
                'name' => $this->bot->getName(),
                'is_healthy' => false,
                'response_time' => round($responseTime * 1000, 2),
                'error' => $e->getMessage(),
            ];
        }
    }

    protected function displayResult(array $result): void
    {
        $status = $result['is_healthy'] ? '✅ Healthy' : '❌ Unhealthy';
        $this->table(
            ['Bot Name', 'Health', 'Response Time', 'Error'],
            [[
                $result['name'],
                $status,
                $result['response_time'] . 'ms',
                $result['error'] ?? 'None',
            ]]
        );
    }
}
