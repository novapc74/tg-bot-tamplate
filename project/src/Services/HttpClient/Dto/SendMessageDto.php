<?php

namespace App\Services\HttpClient\Dto;

final readonly class SendMessageDto implements HttpClientDtoInterface
{
    public function __construct(
        private string $chatId,
        private string $message,
        private array  $options,
    )
    {
    }

    public static function init(string $chatId, string $message, array $options = []): self
    {
        return new self($chatId, $message, $options);
    }

    public function getMethod(): string
    {
        return 'GET';
    }

    public function getParams(): array
    {
        $inlineKeyboard = [
            [
                [
                    "text" => "Открыть Mini App",
                    "url" => 'https://t.me/novapc_bot/novapc_app'
                ]
            ]
        ];

        $inlineKeyboardMarkup = json_encode([
            "inline_keyboard" => $inlineKeyboard
        ]);

        $params = [
            'json' => [
                'chat_id' => $this->chatId,
                'text' => $this->message,
                'parse_mode' => 'Markdown',
                'reply_markup' => $inlineKeyboardMarkup,
            ]
        ];

        if (!empty($this->options)) {
            $params = [
                'json' => array_merge($params['json'], $this->options)
            ];

        }

        return $params;
    }

    public function getApiMethod(): string
    {
        return 'sendMessage';
    }
}
