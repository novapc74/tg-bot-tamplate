<?php

namespace App\Model;

use DateTimeImmutable;

/**
 * Модель для response:
 * клиент ApiCoinGeckoClient::class
 * параметр клиента TopCryptoCurrencyDto
 */
class CryptoCoin
{
    /**
     * bitcoin -> Уникальный идентификатор криптовалюты на платформе CoinGecko.
     */
    private ?string $id;

    /**
     * btc -> Символ криптовалюты (например, BTC для Bitcoin).
     */
    private ?string $symbol;

    /**
     * Bitcoin -> Полное название криптовалюты (например, Bitcoin).
     */
    private ?string $name;

    /**
     * 91535 -> Текущая цена криптовалюты в USD
     */
    private ?float $current_price;

    /**
     * 1 -> Ранг криптовалюты по рыночной капитализации.
     */
    private ?int $market_cap_rank;

    /**
     * 628.51 -> Изменение цены за последние 24 часа
     */
    private ?float $price_change_24h;

    /**
     * 0.69138 -> Процентное изменение цены за последние 24 часа
     */
    private ?float $price_change_percentage_24h;

    private ?DateTimeImmutable $last_updated;

    public function __construct(array $data)
    {
        $this->id = $data['id'];
        $this->symbol = $data['symbol'];
        $this->name = $data['name'];
        $this->current_price = $data['current_price'];
        $this->market_cap_rank = $data['market_cap_rank'];
        $this->price_change_24h = $data['price_change_24h'];
        $this->price_change_percentage_24h = $data['price_change_percentage_24h'];

        try {
            $stringifyDate = $data['last_updated'];
            $this->last_updated = new DateTimeImmutable($stringifyDate);
        } catch (\Exception $e) {
        }
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getSymbol(): ?string
    {
        return $this->symbol;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getCurrentPrice(): ?float
    {
        return $this->current_price;
    }

    public function getMarketCapRank(): ?string
    {
        return $this->market_cap_rank;
    }

    public function getPriceChange24h(): ?float
    {
        return $this->price_change_24h;
    }

    public function getPriceChangePercentage24h(): ?float
    {
        return $this->price_change_percentage_24h;
    }

    public function getLastUpdated(): ?DateTimeImmutable
    {
        return $this->last_updated;
    } // 2025-11-30T15:31:51.948Z

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'symbol' => $this->symbol,
            'name' => $this->name,
            'current_price' => $this->current_price,
            'market_cap_rank' => $this->market_cap_rank,
            'price_change_24h' => $this->price_change_24h,
            'price_change_percentage_24h' => $this->price_change_percentage_24h,
            'last_updated' => $this->last_updated?->format('d.m.Y') ?? 'no update',
        ];

    }

}
