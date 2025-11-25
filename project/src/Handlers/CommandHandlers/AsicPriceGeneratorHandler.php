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

        $options = [
            'parse_mode' => 'MarkdownV2',
        ];

        $this->client->request(
            SendMessageDto::init($chatId, $this->priceProcessing($priceFile), $options)
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

            if (str_contains($line, 'Moscow Stock')) {
                $key = $line;
            }

            if (str_contains($line, 'Moscow (On the way)')) {
                break;
            }

            $items = explode('$', $line);
            if (count($items) === 2) {
                $name = $items[0];
                $price = str_replace([' ', '  '], '', $items[1]);

                $price = (int)$price;
                $price = self::updateItemPrice($price);

                $result[$key][] = str_replace('  ', ' ', '#' . $name . "*$$price*");
            }
        }

        $currentDay = date('d-m-Y');
        $prices = "Прайс от *$currentDay*\n\n";
        foreach ($result as $city => $price) {
            $prices .= "*$city*" . "\n" . implode("\n", $price) . "\n\n";
        }

        $search = ['_', '[', ']', '(', ')', '~', '`', '>', '#', '+', '-', '=', '|', '{', '}', '.', '!'];
        $replace = ['\\_', '\\[', '\\]', '\\(', '\\)', '\\~', '\\`', '\\>', '\\#', '\\+', '\\-', '\\=', '\\|', '\\{', '\\}', '\\.', '\\!'];

        $prices = str_replace($search, $replace, $prices);


        $prices .= <<<EOT
Другие модели по запросу:
Окупаемость [ЗДЕСЬ](https://whattomine.com/asics)
[¦КУПИТЬ¦](https://t.me/jonnyfase)
[¦Чат¦](https://t.me/Mining_KRD_23)¦
[¦Авито¦](https://www.avito.ru/brands/i71930904?src=sharing)¦
[¦Отзывы¦](https://t.me/mining_krd_otziv)¦
[¦USDT¦](https://rapira.net/?ref=06FL)¦
[¦Bybit¦](https://www.bybit.com/invite?ref=RB2PKB)¦
[¦Барахолка¦](https://t.me/mining_baraholka23)¦
[¦АВТОМОБИЛИ¦](https://t.me/ChinaMotors123)
[¦ВИДЕОКАРТЫ¦](https://t.me/alimca_cn)
Отправка по РФ.
[Голосовать](https://t.me/boost/mining_KRD123) ✅ за канал
EOT;


        return $prices;
    }

    private static function updateItemPrice(int $price): int
    {
        $add = match (true) {
            $price >= 0 && $price < 500 => 30,
            $price >= 500 && $price < 1000 => 50,
            $price >= 1000 && $price < 4000 => 100,
            $price > 4000 && $price < 8000 => 150,
            default => 300
        };

        $price += $add;
        $price = ceil($price / 10) * 10;
        $price -= 1;

        return $price;
    }
}
