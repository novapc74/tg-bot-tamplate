<?php

namespace App\Handlers\CommandHandlers;

use App\Handlers\AbstractTelegramBotHandler;
use App\Handlers\PayloadMessageInterface;
use App\Services\HttpClient\Dto\SendMessageDto;

final readonly class ReportCommandHandler extends AbstractTelegramBotHandler
{
    const string COMMAND_NAME = '/report';

    public function handle(PayloadMessageInterface $dto): void
    {
        if (!$chatId = $dto->getChatId()) {
            $this->logger->error('Не установлен ID чата. Прерываем. тело ответа');
            return;
        }

        $promptFile = __DIR__ . '/../../../storage/telegram/prompt.json';

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
Ждем *креды* для нейронки от заказчика, тогда и будет отчет\.
![👍](tg://emoji?id=5368324170671202286)
EOT;

    }
}
