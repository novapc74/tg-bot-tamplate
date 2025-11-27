<?php

namespace App\Services\HttpClient\Dto;

final readonly class SendMessageDto implements HttpClientDtoInterface
{
    public function __construct(
        private string $chatId,
        private string $message,
        private array $options,
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
        $params = [
            'json' => [
                'chat_id' => $this->chatId,
                'text' => $this->message,
                'parse_mode' => 'Markdown',
                'reply_markup' => [
                    'inline_keyboard' => [
                        [
                            [
                                'text' => 'HELP',
                                'callback_data' => '/help',
                            ],
                            [
                                'text' => 'IMAGE',
                                'callback_data' => 'image_action',
                            ],
                            [
                                'text' => 'FORM',
                                'callback_data' => 'form_action',
                            ],
                            [
                                'text' => 'D',
                                'callback_data' => 'action4',
                            ],
                        ],
                        [
                            [
                                'text' => 'Google',
                                'url' => 'https://google.com/',
                            ],
                            [
                                'text' => 'HH',
                                'url' => 'https://hh.ru/',
                            ],
                        ],
                    ],
                ],
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
