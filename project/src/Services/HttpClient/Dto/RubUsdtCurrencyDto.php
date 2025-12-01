<?php

namespace App\Services\HttpClient\Dto;

final readonly class RubUsdtCurrencyDto implements HttpClientDtoInterface
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
                'ids' => 'tether',
                'vs_currencies' => 'rub'
            ]
        ];
    }

    public function getApiMethod(): string
    {
        return 'simple/price';
    }
}
