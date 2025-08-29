<?php

declare(strict_types=1);

use XBot\Telegram\Bot;
use XBot\Telegram\TelegramBot;

it('creates telegram bot from token', function () {
    $bot = Bot::token('123:ABC');

    expect($bot)->toBeInstanceOf(TelegramBot::class);
    expect($bot->getToken())->toBe('123:ABC');
});

