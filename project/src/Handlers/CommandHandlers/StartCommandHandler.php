<?php

namespace App\Handlers\CommandHandlers;

use App\Handlers\AbstractTelegramBotHandler;
use App\Handlers\PayloadMessageInterface;
use App\Services\HttpClient\Dto\SendMessageDto;

final readonly class StartCommandHandler extends AbstractTelegramBotHandler
{
    const string COMMAND_NAME = '/start';
    private const string START_MESSAGE = 'Приветствуем в нашем чате! Для получения инструкции введите "/help"';

    public function handle(PayloadMessageInterface $dto): void
    {
        if (!$chatId = $dto->getChat()?->getId()) {
            $this->logger->error('Не установлен ID чата. Прерываем. тело ответа');
            return;
        }

        $this->client->request(
            SendMessageDto::init($chatId, self::START_MESSAGE)
        );
    }
}
