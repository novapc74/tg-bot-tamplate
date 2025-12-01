<?php

namespace App\Services\HttpClient\Dto;

final readonly class RubUsdtChange24hrCurrencyDto implements HttpClientDtoInterface
{
    public static function init(): self
    {
        return new self();
    }

    public function getMethod(): string
    {
        return 'GET';
    }

    public function getParams(): array
    {
        return [
            'query' => [
                'vs_currency' => 'rub',
                'days' => '1',
                'interval' => 'daily'
            ]
        ];
    }

    public function getApiMethod(): string
    {
        return 'coins/tether/market_chart';
    }
}
