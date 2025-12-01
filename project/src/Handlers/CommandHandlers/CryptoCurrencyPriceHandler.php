<?php

namespace App\Handlers\CommandHandlers;

use App\Enum\FileHelper;
use App\Handlers\TelegramPayloadInterface;
use App\Handlers\AbstractTelegramBotHandler;
use App\Services\HttpClient\Dto\SendMessageDto;

final readonly class CryptoCurrencyPriceHandler extends AbstractTelegramBotHandler
{
    const string COMMAND_NAME = '/crypto_price';
    public function handle(TelegramPayloadInterface $dto): void
    {
        $chatId = $dto->getChat()->getId();

        $cryptoPrice = FileHelper::CRYPTO_PRICE_FILE_PATH->value;

        if (!is_file($cryptoPrice)) {
            $this->client->request(SendMessageDto::init(
                $chatId,
                'Файл отсутствует, нужно сгенерировать',
            ));
        }

        $content = file_get_contents($cryptoPrice);

        $this->client->request(
            SendMessageDto::init($chatId, $content, [
                    'parse_mode' => 'MarkdownV2',
                ]
            )
        );
    }
}
