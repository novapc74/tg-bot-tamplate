<?php

namespace App\Handlers;

use App\Model\Chat;

final class PayloadDto implements PayloadMessageInterface
{
    private ?Chat $chat = null;

    public function __construct(private readonly array $body)
    {
    }

    public static function init(array $requestPayload): self
    {
        $instance = new self($requestPayload);
        $instance->makeModels($requestPayload);

        return $instance;
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
        if ($this->chat === null) {
            self::makeModels($this->body);
        }

        return $this->chat;
    }

    private function makeModels(array $body): void
    {
        static $counter = 0;

        if (empty($body)) {
            return;
        }

        $modelData = '';
        foreach ($this->body as $key => $modelData) {
            if ($key === 'chat' && is_array($modelData)) {
                $this->chat = new Chat($modelData);
                return;
            }

            if (is_array($modelData)) {
                self::makeModels($modelData);
            }
        }
    }
}
