<?php

namespace App\Handlers;

use Psr\Log\LoggerInterface;
use App\Services\HttpClient\Client\CustomHttpClientInterface;

abstract readonly class AbstractTelegramBotHandler
{
    public function __construct(
        protected CustomHttpClientInterface $client,
        protected LoggerInterface   $logger
    )
    {
    }

    abstract public function handle(TelegramPayloadInterface $dto): void;
}

