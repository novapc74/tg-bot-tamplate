<?php

namespace App\Handlers\CommandHandlers;

use App\Enum\FileHelper;
use App\Handlers\AbstractTelegramBotHandler;
use App\Handlers\TelegramPayloadInterface;
use App\Services\HttpClient\Dto\SendMessageDto;

final readonly class ShowPromptCommandHandler extends AbstractTelegramBotHandler
{
    const string COMMAND_NAME = '/prompt';

    public function handle(TelegramPayloadInterface $dto): void
    {
        if (!$chatId = $dto->getChat()?->getId()) {
            $this->logger->error('Не установлен ID чата. Прерываем. тело ответа');
            return;
        }

        $promptFile = FileHelper::PROMPT_FILE_PATH->value;
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
