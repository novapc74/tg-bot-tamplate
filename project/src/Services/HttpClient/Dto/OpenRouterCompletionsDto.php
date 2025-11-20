<?php

namespace App\Services\HttpClient\Dto;

final readonly class OpenRouterCompletionsDto implements HttpClientDtoInterface
{
    public function __construct(
        private string $message,
        private array  $options,
    )
    {
    }

    public static function init(string $message, array $options = []): self
    {
        return new self($message, $options);
    }

    public function getMethod(): string
    {
        return 'POST';
    }

    /**
     * Структура запроса для получения форматированного ответа.
     * https://openrouter.ai/docs/features/structured-outputs
     */
    public function getParams(): array
    {
        $params = [
            'json' => [
                'model' => 'mistralai/mistral-7b-instruct:free',
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => $this->message,
                    ]
                ],
                'max_tokens' => 500,
//                'response_format' => [
//                    'type' => 'json_schema',
//                    'json_schema' => [
//                        'name' => '',
//                        'strict' => true,
//                        'schema' => [
//                            'type' => 'object',
//                            'properties' => [
//                                'location' => [
//                                    'type' => 'string',
//                                    'description' => ''
//                                ],
//                                'temperature' => [
//                                    'type' => 'string',
//                                    'description' => ''
//                                ],
//                                'conditions' => [
//                                    'type' => 'string',
//                                    'description' => ''
//                                ]
//                            ],
//                        ],
//                        "required" => ["location", "temperature", "conditions"],
//                        "additionalProperties" => false
//                    ]
//                ]
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
        return 'completions';
    }
}
