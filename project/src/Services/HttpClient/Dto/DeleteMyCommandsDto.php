<?php

namespace App\Services\HttpClient\Dto;

final readonly class DeleteMyCommandsDto implements HttpClientDtoInterface
{
    public static function init(): self
    {
        return new self();
    }

    public function getMethod(): string
    {
        return 'POST';
    }

    public function getParams(): array
    {
        return [
            'json' => []
        ];

    }

    public function getApiMethod(): string
    {
        return 'deleteMyCommands';
    }
}
