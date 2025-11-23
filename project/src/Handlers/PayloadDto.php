<?php

namespace App\Handlers;

use InvalidArgumentException;

final readonly class PayloadDto implements PayloadMessageInterface
{
    public function __construct(private array $message)
    {
    }

    public static function init(array $requestPayload): self
    {
        if ($message = $requestPayload['message']) {
            return new self($message);
        }

        throw new InvalidArgumentException('Message parameter must be provided');
    }

    public function getText(): ?string
    {
        if ($text = $this->message['text'] ?? null) {
            return $text;
        }

        return null;
    }

    public function getChatId(): ?string
    {
        if ($chatId = $this->message['chat']['id'] ?? null) {
            return $chatId;
        }

        return null;
    }
}
