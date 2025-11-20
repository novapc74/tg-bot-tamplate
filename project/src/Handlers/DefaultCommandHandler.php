<?php

namespace App\Handlers;

use Psr\Log\LoggerInterface;
use App\Services\HttpClient\Dto\SendMessageDto;
use App\Services\HttpClient\Client\ApiTelegramClient;
use App\Services\HttpClient\Dto\OpenRouterResponseDto;
use App\Services\HttpClient\Client\ApiOpenRouterClient;
use App\Services\HttpClient\Dto\OpenRouterCompletionsDto;

final readonly class DefaultCommandHandler extends AbstractTelegramBotHandler
{
    public function __construct(
        ApiOpenRouterClient|ApiTelegramClient $client,
        LoggerInterface                       $logger,
        private ApiTelegramClient             $tgClient
    )
    {
        parent::__construct($client, $logger);
    }

    public function handle(array $data): void
    {
        if (!$chatId = $data['message']['chat']['id'] ?? null) {
            $this->logger->error('Не установлен ID чата. Прерываем. ' . json_encode($data, JSON_UNESCAPED_UNICODE));
            return;
        }

        if (!$userMessage = $data['message']['text'] ?? null) {
            $this->logger->error('пользователь не передал текст в сообщении. Прерываем. ' . json_encode($data, JSON_UNESCAPED_UNICODE));
            return;
        }

        if (empty($userMessage)) {
            return;
        }

        $response = $this->client->request(
            OpenRouterCompletionsDto::init($userMessage)
        );

        if ($response === null) {
            $this->logger->error('Нейронка вернула пустой отввет');
            $this->tgClient->request(
                SendMessageDto::init($chatId, trim('Ошибка, пустой ответ на запрос. Возможно, исчерпан лимит бесплатных запросов.'))
            );
            return;
        }

        $data = OpenRouterResponseDto::init($response);

        $content = str_replace(['<s>', 'OUT', '/OUT'], '', $data->getContent() ?? 'Пустой ответ, попробуйте снова.');

        if (empty($content)) {
            $content = 'Не удалось ответить на ваш вопрос, попробуйте еще раз.';
        }

        $this->tgClient->request(
            SendMessageDto::init($chatId, trim($content))
        );
    }
}
