<?php

namespace App\Traits;

trait PriceTrait
{
    public static function priceProcessing(string $file): string
    {
        $fileContent = file_get_contents($file);

        $fileContent = str_replace('🇷🇺', '', $fileContent);

        $data = explode("\n", $fileContent);

        $result = [];
        $key = null;
        foreach ($data as $line) {
            $line = trim($line);

            if (str_contains($line, 'Moscow Stock')) {
                $key = 'Наличе:';
            }

            if (str_contains($line, 'Moscow (On the way)')) {
                break;
            }

            $items = explode('$', $line);
            if (count($items) === 2) {
                $name = $items[0];
                $price = str_replace([' ', '  '], '', $items[1]);

                $price = (int)$price;
                $price = self::calculatePriceItem($price);

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

        return $prices . self::getFooter();
    }

    private static function calculatePriceItem(int $price): int
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

    private static function getFooter():string
    {
        return <<<EOT
Другие модели по запросу:
Окупаемость [ЗДЕСЬ](https://whattomine.com/asics)

🔥 🔥 🔥 🔥 🔥 🔥 🔥 🔥 🔥

[¦КУПИТЬ¦](https://t.me/jonnyfase)
[¦Чат¦](https://t.me/Mining_KRD_23)
[¦Авито¦](https://www.avito.ru/brands/i71930904?src=sharing)
[¦Отзывы¦](https://t.me/mining_krd_otziv)
[¦USDT¦](https://rapira.net/?ref=06FL)
[¦Bybit¦](https://www.bybit.com/invite?ref=RB2PKB)
[¦Барахолка¦](https://t.me/mining_baraholka23)
[¦АВТОМОБИЛИ¦](https://t.me/ChinaMotors123)
[¦ВИДЕОКАРТЫ¦](https://t.me/alimca_cn)
Отправка по РФ\.
[Голосовать](https://t.me/boost/mining_KRD123) ✅ за канал
EOT;
    }
}
