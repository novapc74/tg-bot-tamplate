<?php

namespace App\Handlers\CommandHandlers;

use App\Handlers\PayloadMessageInterface;
use App\Handlers\AbstractTelegramBotHandler;
use App\Services\HttpClient\Dto\SendMessageDto;

final readonly class AsicPriceGeneratorHandler extends AbstractTelegramBotHandler
{
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

        $this->client->request(
            SendMessageDto::init($chatId, $this->priceProcessing($priceFile))
        );
    }

    private static function priceProcessing(string $file): string
    {
        $fileContent = file_get_contents($file);

        $fileContent = str_replace('🇷🇺', '', $fileContent);

        $data = explode("\n", $fileContent);

        $result = [];
        $key = null;
        foreach ($data as $line) {
            $line = trim($line);

            if (str_contains($line, 'Moscow Stock') || str_contains($line, 'Moscow (On the way)')) {
                $key = $line;
            }

            $items = explode('$', $line);
            if (count($items) === 2) {
                $name = $items[0];
                $price = str_replace([' ', '  '], '', $items[1]);

                $result[$key][] = str_replace('  ', ' ', '🇷🇺' . $name . ' $' . $price + 10);
            }
        }

        $currentDay = date('d-m-Y');
        $prices = "🎉 $currentDay. Ваши цены:\n\n";
        foreach ($result as $city => $price) {
            $prices .= $city . "\n" . implode("\n", $price) . "\n\n";
        }


        return $prices;
    }

    private static function updateItemPrice(int|string &$price): void
    {
        $price = $price + 10;
    }
}
