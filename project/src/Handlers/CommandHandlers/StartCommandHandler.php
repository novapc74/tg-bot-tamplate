<?php

namespace App\Handlers\CommandHandlers;

use App\Handlers\AbstractTelegramBotHandler;
use App\Services\HttpClient\Dto\SendMessageDto;

final readonly class StartCommandHandler extends AbstractTelegramBotHandler
{
    const string COMMAND_NAME = '/start';
    private const string START_MESSAGE = 'Приветствуем в нашем чате! Для получения инструкции введите "/help"';
    public function handle(array $data): void
    {
        if(!$chatId = $data['message']['chat']['id'] ?? null) {
            $this->logger->error('Не установлен ID чата. Прерываем.' . json_encode($data));
            return;
        }

        $this->client->request(
            SendMessageDto::init($chatId, self::START_MESSAGE)
        );
    }
}
