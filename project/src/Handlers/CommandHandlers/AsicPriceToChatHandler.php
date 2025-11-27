<?php

namespace App\Handlers\CommandHandlers;

use App\Traits\PriceTrait;
use App\Handlers\TelegramPayloadInterface;
use App\Handlers\AbstractTelegramBotHandler;
use App\Services\HttpClient\Dto\SendMessageDto;

readonly class AsicPriceToChatHandler extends AbstractTelegramBotHandler
{
    use PriceTrait;

    public function handle(TelegramPayloadInterface $dto): void
    {
        $chatId = '-1003373031540';

        $priceFile = __DIR__ . '/../../../storage/telegram/price.txt';

        if (!is_file($priceFile)) {
            $this->logger->error('Не найден файл по пути: ' . $priceFile);
            return;
        }

        $options = [
            'parse_mode' => 'MarkdownV2',
        ];

        $this->client->request(
            SendMessageDto::init(
                $chatId,
                self::priceProcessing($priceFile),
                $options
            )
        );
    }
}
