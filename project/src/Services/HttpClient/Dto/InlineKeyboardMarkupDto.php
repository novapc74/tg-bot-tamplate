<?php

namespace App\Services\HttpClient\Dto;

final readonly class InlineKeyboardMarkupDto implements HttpClientDtoInterface
{
    public function __construct(
        private array $options,
    )
    {
    }

    public static function init(array $options): self
    {
        return new self($options);
    }

    public function getMethod(): string
    {
        return 'POST';
    }

    public function getParams(): array
    {
        $params =  [
            'json' => []
        ];

        if ($options = $this->options) {
            $options = array_merge($params['json'], $options);
            $params['json'] = $options;
        }

        return $params;
    }

    public function getApiMethod(): string
    {
        return 'sendMessage';
    }
}
