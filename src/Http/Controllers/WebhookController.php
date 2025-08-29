<?php

declare(strict_types=1);

namespace XBot\Telegram\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use XBot\Telegram\Exceptions\TelegramException;
use XBot\Telegram\Models\DTO\Update;
use XBot\Telegram\TelegramBot;

class WebhookController extends Controller
{
    protected TelegramBot $bot;

    public function __construct(TelegramBot $bot)
    {
        $this->bot = $bot;
    }

    public function handle(Request $request): JsonResponse
    {
        $botName = $this->bot->getName();

        try {
            $data = $this->validateRequest($request);
            $update = Update::fromArray($data);
            $update->validate();

            $this->fireWebhookEvent($botName, $update, $request);
            $this->logWebhookRequest($botName, $update, $request);

            return response()->json(['ok' => true]);
        } catch (TelegramException $e) {
            $this->logWebhookError($botName, $e, $request);

            return response()->json([
                'ok' => false,
                'error' => $e->getMessage(),
                'error_code' => $e->getCode(),
            ], 400);
        } catch (\Throwable $e) {
            $this->logWebhookError($botName, $e, $request);

            return response()->json([
                'ok' => false,
                'error' => 'Internal server error',
            ], 500);
        }
    }

    protected function validateRequest(Request $request): array
    {
        $data = $request->json()->all();

        if (empty($data)) {
            throw new \InvalidArgumentException('Request body is empty or invalid JSON');
        }

        if (! isset($data['update_id'])) {
            throw new \InvalidArgumentException('Missing update_id in request');
        }

        return $data;
    }

    protected function fireWebhookEvent(string $botName, Update $update, Request $request): void
    {
        if (! function_exists('event')) {
            return;
        }

        event('telegram.webhook.received', [$botName, $update, $request]);
        $updateType = $update->getType();
        event("telegram.webhook.{$updateType}", [$botName, $update, $request]);
        event("telegram.{$botName}.webhook.received", [$update, $request]);
        event("telegram.{$botName}.webhook.{$updateType}", [$update, $request]);
    }

    protected function logWebhookRequest(string $botName, Update $update, Request $request): void
    {
        if (! function_exists('logger')) {
            return;
        }

        $logLevel = config("telegram.logging.level", 'info');
        $logEnabled = config("telegram.logging.enabled", true);

        if (! $logEnabled) {
            return;
        }

        logger()->log($logLevel, 'Telegram webhook received', [
            'bot_name' => $botName,
            'update_id' => $update->updateId,
            'update_type' => $update->getType(),
            'chat_id' => $update->getChat()?->id,
            'user_id' => $update->getUser()?->id,
            'request_ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
    }

    protected function logWebhookError(string $botName, \Throwable $error, Request $request): void
    {
        if (! function_exists('logger')) {
            return;
        }

        logger()->error('Telegram webhook error', [
            'bot_name' => $botName,
            'error' => $error->getMessage(),
            'error_class' => get_class($error),
            'request_data' => $request->json()->all(),
            'request_ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'trace' => $error->getTraceAsString(),
        ]);
    }
}
