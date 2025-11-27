<?php

namespace App\Handlers\CommandHandlers;

use App\Handlers\AbstractTelegramBotHandler;
use App\Handlers\PayloadMessageInterface;
use App\Services\HttpClient\Dto\SendMessageDto;

final readonly class ShowPromptCommandHandler extends AbstractTelegramBotHandler
{
    const string COMMAND_NAME = '/prompt';

    public function handle(PayloadMessageInterface $dto): void
    {
        if (!$chatId = $dto->getChat()?->getId()) {
            $this->logger->error('Не установлен ID чата. Прерываем. тело ответа');
            return;
        }

        $promptFile = __DIR__ . '/../../../storage/telegram/prompt.json';
        if (file_exists($promptFile)) {
            $fileData = file_get_contents($promptFile);
            $text = <<<EOT
*Текущий prompt:*
```json
$fileData
```
EOT;

            $options = [
                'parse_mode' => 'MarkdownV2',
            ];

            $this->client->request(
                SendMessageDto::init($chatId, $text, $options)
            );
        }
    }
}
