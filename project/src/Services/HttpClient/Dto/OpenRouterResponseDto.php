<?php

namespace App\Services\HttpClient\Dto;

use App\Services\HttpClient\Client\HttpClientResponseInterface;

readonly class OpenRouterResponseDto
{
    public function __construct(private HttpClientResponseInterface $response)
    {
    }

    public static function init(HttpClientResponseInterface $response): self
    {
        return new self($response);
    }

    public function getContent(): ?string
    {
        return $this->response->getData()['choices'][0]['message']['content'] ?? null;
    }

}
