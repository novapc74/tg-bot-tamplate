<?php

namespace App\Handlers;

final readonly class PayloadDto implements PayloadMessageInterface
{
    public function __construct(private array $body)
    {
    }

    public static function init(array $requestPayload): self
    {
        return new self($requestPayload);
    }

    public function getText(): ?string
    {
        if ($text = $this->body['message']['text'] ?? null) {
            return $text;
        }

        if ($text = $this->body['channel_post']['text'] ?? null) {
            return $text;
        }

        return null;
    }

    public function getChatId(): ?string
    {
        if ($chatId = $this->body['message']['chat']['id'] ?? null) {
            return $chatId;
        }

        if ($chatId = $this->body['channel_post']['chat']['id'] ?? null) {
            return $chatId;
        }

        return null;
    }
}
