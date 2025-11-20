<?php

namespace App\Services\HttpClient\Dto;

final readonly class GetUpdatesDto implements HttpClientDtoInterface
{
    public function __construct(private array $options = [])
    {
    }

    public static function init(array $options = []): self
    {
        return new self($options);
    }

    public function getMethod(): string
    {
        return 'GET';
    }

    public function getParams(): array
    {
        return $this->options;
    }

    public function getApiMethod(): string
    {
        return 'getUpdates';
    }
}
