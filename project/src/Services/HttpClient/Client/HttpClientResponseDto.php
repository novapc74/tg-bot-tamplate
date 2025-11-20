<?php

namespace App\Services\HttpClient\Client;

final readonly class HttpClientResponseDto implements HttpClientResponseInterface
{
    public function __construct(private array $data)
    {
    }

    public static function init(array $data): self
    {
        return new self($data);
    }

    public function getData(): array
    {
        return $this->data;
    }
}
