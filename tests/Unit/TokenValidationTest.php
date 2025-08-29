<?php

declare(strict_types=1);

use XBot\Telegram\TelegramBot;
use XBot\Telegram\Tests\Support\FakeHttpClient;
use XBot\Telegram\Exceptions\ConfigurationException;

it('accepts valid token with default validation', function () {
    $bot = new TelegramBot('main', new FakeHttpClient('123456789:ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghi'));
    expect($bot)->toBeInstanceOf(TelegramBot::class);
});

it('throws exception for invalid token format by default', function () {
    expect(fn() => new TelegramBot('main', new FakeHttpClient('invalid-token')))
        ->toThrow(ConfigurationException::class);
});

it('allows overriding token pattern', function () {
    $bot = new TelegramBot('main', new FakeHttpClient('test_token'), [
        'token_validation' => ['pattern' => '/^test_token$/'],
    ]);

    expect($bot->getToken())->toBe('test_token');
});

it('can disable token validation', function () {
    $bot = new TelegramBot('main', new FakeHttpClient('invalid token'), [
        'token_validation' => ['enabled' => false],
    ]);

    expect($bot->getToken())->toBe('invalid token');
});
