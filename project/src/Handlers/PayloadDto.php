<?php

namespace App\Handlers;

use App\Model\Chat;
use App\Traits\ModelTrait;

final class PayloadDto implements PayloadMessageInterface
{
    use ModelTrait;

    public function __construct(private readonly array $body)
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

    public function getChat(): Chat
    {
        return self::getModel($this->body, 'chat', Chat::class);
    }
}
