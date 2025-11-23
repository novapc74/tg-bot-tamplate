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

    public function handle(PayloadMessageInterface $dto): void
    {
        if (!$chatId = $dto->getChatId()) {
            $this->logger->error('Не установлен ID чата. Прерываем.');
            return;
        }

        if (!$userMessage = $dto->getText()) {
            $this->logger->error('пользователь не передал текст в сообщении. Прерываем.');
            return;
        }

        if (empty($userMessage)) {
            return;
        }

        $content = sprintf(
            'Пока не отправляем запросы в нейронку, настраиваем prompt. Вы отправилисообщение %s',
            $userMessage
        );
//        if(!$content = $this->getOpenRouterResult($userMessage)) {
//            $this->logger->error('Нейронка вернула пустой отввет');
//            $this->tgClient->request(
//                SendMessageDto::init($chatId, trim('Ошибка, пустой ответ на запрос. Возможно, исчерпан лимит бесплатных запросов.'))
//            );
//            return;
//        }

        $this->tgClient->request(
            SendMessageDto::init($chatId, $content)
        );
    }

    private function getOpenRouterResult(string $userMessage): ?string
    {
        $response = $this->client->request(
            OpenRouterCompletionsDto::init($userMessage)
        );

        if ($response === null) {
            return null;
        }

        $data = OpenRouterResponseDto::init($response);

        $content = $this->sanitizeOpenRouterResponseContent(
            $data->getContent() ?? 'Пустой ответ, попробуйте снова.'
        );

        return trim($content);
    }

    private function sanitizeOpenRouterResponseContent(string $content): string
    {
        $content = str_replace(['<s>', 'OUT', '/OUT'], '', $content);

        return trim($content);
    }
}
