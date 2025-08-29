<?php

declare(strict_types=1);

namespace XBot\Telegram\Console\Commands;

use Illuminate\Console\Command;
use XBot\Telegram\TelegramBot;

/**
 * Telegram Webhook management command.
 */
class TelegramWebhookCommand extends Command
{
    protected $signature = 'telegram:webhook
                           {action : Action to perform (set|delete|info)}
                           {--url= : Webhook URL (required for set action)}
                           {--drop-pending : Drop pending updates when deleting webhook}
                           {--secret= : Webhook secret token}
                           {--certificate= : Path to certificate file}
                           {--max-connections=100 : Maximum allowed number of simultaneous connections}
                           {--allowed-updates=* : List of allowed update types}';

    protected $description = 'Manage Telegram bot webhook';

    protected TelegramBot $bot;

    public function __construct(TelegramBot $bot)
    {
        parent::__construct();
        $this->bot = $bot;
    }

    public function handle(): int
    {
        $action = $this->argument('action');

        return match ($action) {
            'set' => $this->setWebhook(),
            'delete' => $this->deleteWebhook(),
            'info' => $this->getWebhookInfo(),
            default => $this->invalidAction($action),
        };
    }

    protected function setWebhook(): int
    {
        $url = $this->option('url');
        if (empty($url)) {
            $this->error('Webhook URL is required for set action. Use --url option.');
            return 1;
        }

        $options = $this->buildWebhookOptions();
        $this->info("Setting webhook: {$url}");

        $success = $this->bot->setWebhook($url, $options);

        if ($success) {
            $this->info('✅ Webhook set successfully');
            return 0;
        }

        $this->error('❌ Failed to set webhook');
        return 1;
    }

    protected function deleteWebhook(): int
    {
        $dropPending = $this->option('drop-pending');
        if ($dropPending) {
            $this->info('Dropping pending updates...');
        }

        $success = $this->bot->deleteWebhook($dropPending);

        if ($success) {
            $this->info('✅ Webhook deleted successfully');
            return 0;
        }

        $this->error('❌ Failed to delete webhook');
        return 1;
    }

    protected function getWebhookInfo(): int
    {
        $this->info('Webhook information:');
        $info = $this->bot->getWebhookInfo();
        $this->displayWebhookInfo($info);
        return 0;
    }

    protected function buildWebhookOptions(): array
    {
        $options = [];

        if ($secret = $this->option('secret')) {
            $options['secret_token'] = $secret;
        }

        if ($certificate = $this->option('certificate')) {
            if (file_exists($certificate)) {
                $options['certificate'] = $certificate;
            } else {
                $this->warn("Certificate file not found: {$certificate}");
            }
        }

        if ($maxConnections = $this->option('max-connections')) {
            $options['max_connections'] = (int) $maxConnections;
        }

        if ($allowedUpdates = $this->option('allowed-updates')) {
            $options['allowed_updates'] = $allowedUpdates;
        }

        return $options;
    }

    protected function displayWebhookInfo(array $info): void
    {
        $tableData = [
            ['URL', $info['url'] ?: 'Not set'],
            ['Has Custom Certificate', $info['has_custom_certificate'] ? 'Yes' : 'No'],
            ['Pending Update Count', $info['pending_update_count'] ?? 0],
            ['Max Connections', $info['max_connections'] ?? 'Default'],
        ];

        if (! empty($info['ip_address'])) {
            $tableData[] = ['IP Address', $info['ip_address']];
        }

        if (! empty($info['last_error_date'])) {
            $tableData[] = ['Last Error Date', date('Y-m-d H:i:s', $info['last_error_date'])];
            $tableData[] = ['Last Error Message', $info['last_error_message'] ?? 'Unknown'];
        }

        if (! empty($info['allowed_updates'])) {
            $tableData[] = ['Allowed Updates', implode(', ', $info['allowed_updates'])];
        }

        $this->table(['Property', 'Value'], $tableData);
    }

    protected function invalidAction(string $action): int
    {
        $this->error("Invalid action: {$action}");
        $this->info('Available actions: set, delete, info');
        return 1;
    }
}
