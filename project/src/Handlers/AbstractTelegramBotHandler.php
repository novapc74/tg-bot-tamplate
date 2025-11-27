<?php

namespace App\Handlers;

use Psr\Log\LoggerInterface;
use App\Services\HttpClient\Client\ApiTelegramClient;
use App\Services\HttpClient\Client\ApiOpenRouterClient;

abstract readonly class AbstractTelegramBotHandler
{
    public function __construct(
        protected ApiTelegramClient|ApiOpenRouterClient $client,
        protected LoggerInterface   $logger
    )
    {
    }

    abstract public function handle(TelegramPayloadInterface $dto): void;
}
