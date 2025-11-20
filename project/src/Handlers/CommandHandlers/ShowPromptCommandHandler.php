<?php

namespace App\Handlers\CommandHandlers;

use App\Handlers\AbstractTelegramBotHandler;
use App\Services\HttpClient\Dto\SendMessageDto;

final readonly class ShowPromptCommandHandler extends AbstractTelegramBotHandler
{
    const string COMMAND_NAME = '/prompt';

    public function handle(array $data): void
    {
        if (!$chatId = $data['message']['chat']['id'] ?? null) {
            $this->logger->error('Не установлен ID чата. Прерываем. тело ответа' . json_encode($data, JSON_UNESCAPED_UNICODE));
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
