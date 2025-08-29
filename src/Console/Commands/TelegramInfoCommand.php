<?php

declare(strict_types=1);

namespace XBot\Telegram\Console\Commands;

use Illuminate\Console\Command;
use XBot\Telegram\TelegramBot;

/**
 * Telegram bot information command.
 */
class TelegramInfoCommand extends Command
{
    protected $signature = 'telegram:info {--json}';

    protected $description = 'Show Telegram bot information';

    protected TelegramBot $bot;

    public function __construct(TelegramBot $bot)
    {
        parent::__construct();
        $this->bot = $bot;
    }

    public function handle(): int
    {
        $info = $this->getBotInfo();

        if ($this->option('json')) {
            $this->info(json_encode($info, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        } else {
            $this->displayInfo($info);
        }

        return 0;
    }

    protected function getBotInfo(): array
    {
        $botInfo = $this->bot->getMe();
        $stats = $this->bot->getStats();

        return [
            'name' => $this->bot->getName(),
            'bot_info' => $botInfo->toArray(),
            'stats' => $stats,
        ];
    }

    protected function displayInfo(array $info): void
    {
        $this->info('Telegram Bot Information');
        $this->line('');

        $botInfo = $info['bot_info'];
        $this->table(
            ['Property', 'Value'],
            [
                ['ID', $botInfo['id']],
                ['Username', $botInfo['username'] ?? 'N/A'],
                ['First Name', $botInfo['first_name']],
                ['Is Bot', $botInfo['is_bot'] ? 'Yes' : 'No'],
            ]
        );

        $stats = $info['stats'];
        $this->line('');
        $this->info('Statistics:');
        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Calls', $stats['total_calls']],
                ['Successful Calls', $stats['successful_calls']],
                ['Failed Calls', $stats['failed_calls']],
                ['Success Rate', number_format($stats['success_rate'], 2) . '%'],
                ['Uptime', $stats['uptime_formatted']],
                ['Last Call', $stats['last_call_time'] ? date('Y-m-d H:i:s', $stats['last_call_time']) : 'Never'],
            ]
        );
    }
}
