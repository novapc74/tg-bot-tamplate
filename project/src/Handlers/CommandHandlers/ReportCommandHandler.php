<?php

namespace App\Handlers\CommandHandlers;

use App\Enum\FileHelper;
use App\Handlers\TelegramPayloadInterface;
use App\Handlers\AbstractTelegramBotHandler;
use App\Services\HttpClient\Dto\SendMessageDto;

final readonly class ReportCommandHandler extends AbstractTelegramBotHandler
{
    const string COMMAND_NAME = '/report';

    public function handle(TelegramPayloadInterface $dto): void
    {
        if (!$chatId = $dto->getChat()?->getId()) {
            $this->logger->error('Не установлен ID чата. Прерываем. тело ответа');
            return;
        }

        $promptFile = FileHelper::PROMPT_FILE_PATH->value;

        if (!is_file($promptFile)) {
            $this->logger->error('Не найден файл по пути: ' . $promptFile);
            return;
        }

        #TODO сделать запрос в нейронку, обработать и отрисовать ответ (текст + картинки, если будут)

        $this->client->request(
            SendMessageDto::init($chatId, $this->makeReport(), ['parse_mode' => 'MarkdownV2',])
        );
    }

    private function makeReport(): string
    {
        return <<<EOT
Настроим промпт, будет результат\.![👍](tg://emoji?id=5368324170671202286)
EOT;
    }
}
