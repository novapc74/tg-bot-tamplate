<?php

namespace App\Services\HttpClient\Dto;

final readonly class TopCryptoCurrencyDto implements HttpClientDtoInterface
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
                'vs_currency' => 'usd',
                'order' => 'market_cap_desc',
                'per_page' => 5,
                'page' => 1,
                'sparkline' => 'false'
            ]
        ];
    }

    public function getApiMethod(): string
    {
        return 'coins/markets';
    }
}
