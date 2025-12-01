<?php

namespace App\Services\HttpClient\Dto;

final readonly class CryptoPriceUsdtDto implements HttpClientDtoInterface
{
    public function __construct(private array $cryptoPairs)
    {
    }

    public static function init(array $crypto_pairs): self
    {
        return new self($crypto_pairs);
    }

    public function getMethod(): string
    {
        return 'GET';
    }

    public function getParams(): array
    {
        return  [
            'query' => [
                'ids' => implode(',', $this->cryptoPairs),
                'vs_currencies' => 'usd'
            ]
        ];
    }

    public function getApiMethod(): string
    {
        return 'simple/price';
    }
}
