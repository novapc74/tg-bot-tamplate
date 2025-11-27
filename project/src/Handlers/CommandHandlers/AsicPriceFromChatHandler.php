<?php

namespace App\Handlers\CommandHandlers;

use App\Traits\PriceTrait;
use App\Handlers\PayloadMessageInterface;
use App\Handlers\AbstractTelegramBotHandler;
use App\Services\HttpClient\Dto\SendMessageDto;

final readonly class AsicPriceFromChatHandler extends AbstractTelegramBotHandler
{
    use PriceTrait;

    const string COMMAND_NAME = '-1003373031540';

    public function handle(PayloadMessageInterface $dto): void
    {
        $chatId = $dto->getChat()->getId();
        $price = $dto->getText();

        if (!str_contains($price, 'Moscow Stock')) {
            $this->logger->error('Сообщение на прайс не похоже. Тело: ' . json_encode($price, JSON_UNESCAPED_UNICODE));
            return;
        }

        $options = [
            'parse_mode' => 'MarkdownV2',
        ];

        $priceDir = __DIR__ . '/../../../storage/telegram/';
        if (!is_dir($priceDir)) {
            mkdir($priceDir, 0755, true);
        }

        $fileName = $priceDir . 'price.txt';
        file_put_contents($fileName, $price);

        $this->logger->info(sprintf('Отправляем прайс в чат: %s', $chatId));

        $this->client->request(
            SendMessageDto::init(
                $chatId,
                self::getMessage(),
                $options
            )
        );
    }

    private static function getMessage(): string
    {
        return <<<EOT
Прайс обработан, можно генерировать\.
Перейдите в бот `@novapc_bot` и вызовите команду `/asic_price`
EOT;
    }
}
