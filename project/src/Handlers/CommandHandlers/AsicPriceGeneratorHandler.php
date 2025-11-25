<?php

namespace App\Handlers\CommandHandlers;

use App\Handlers\PayloadMessageInterface;
use App\Handlers\AbstractTelegramBotHandler;
use App\Services\HttpClient\Dto\SendMessageDto;
use App\Traits\PriceTrait;

final readonly class AsicPriceGeneratorHandler extends AbstractTelegramBotHandler
{
    use PriceTrait;
    const string COMMAND_NAME = '/asic_price';

    public function handle(PayloadMessageInterface $dto): void
    {
        if (!$chatId = $dto->getChatId()) {
            $this->logger->error('Не установлен ID чата. Прерываем. тело ответа');
            return;
        }

        $priceFile = __DIR__ . '/../../../storage/telegram/price.txt';

        if (!is_file($priceFile)) {
            $this->logger->error('Не найден файл по пути: ' . $priceFile);
            return;
        }

        $options = [
            'parse_mode' => 'MarkdownV2',
        ];

        $this->client->request(
            SendMessageDto::init($chatId, self::priceProcessing($priceFile), $options)
        );
    }
}
