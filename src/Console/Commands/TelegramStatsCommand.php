<?php

declare(strict_types=1);

namespace XBot\Telegram\Console\Commands;

use Illuminate\Console\Command;
use XBot\Telegram\TelegramBot;

/**
 * Telegram bot statistics command.
 */
class TelegramStatsCommand extends Command
{
    protected $signature = 'telegram:stats {--json}';

    protected $description = 'Show Telegram bot statistics';

    protected TelegramBot $bot;

    public function __construct(TelegramBot $bot)
    {
        parent::__construct();
        $this->bot = $bot;
    }

    public function handle(): int
    {
        $stats = $this->bot->getStats();

        if ($this->option('json')) {
            $this->info(json_encode($stats, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        } else {
            $this->displayStats($stats);
        }

        return 0;
    }

    protected function displayStats(array $stats): void
    {
        $this->info('Telegram Bot Statistics');
        $this->line('');

        $this->table(
            ['Metric', 'Value'],
            [
                ['Name', $stats['name']],
                ['Total Calls', number_format($stats['total_calls'])],
                ['Successful Calls', number_format($stats['successful_calls'])],
                ['Failed Calls', number_format($stats['failed_calls'])],
                ['Success Rate', number_format($stats['success_rate'], 2) . '%'],
                ['Uptime', $stats['uptime_formatted']],
                ['Created At', date('Y-m-d H:i:s', $stats['created_at'])],
                ['Last Call', $stats['last_call_time'] ? date('Y-m-d H:i:s', $stats['last_call_time']) : 'Never'],
            ]
        );
    }
}
