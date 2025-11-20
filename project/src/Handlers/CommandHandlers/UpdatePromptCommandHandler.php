<?php

namespace App\Handlers\CommandHandlers;

use App\Handlers\AbstractTelegramBotHandler;
use App\Services\HttpClient\Dto\SendMessageDto;

final readonly class UpdatePromptCommandHandler extends AbstractTelegramBotHandler
{
    const string COMMAND_NAME = '#update_prompt#';
    private const string PROMPT_FILE_NAME = 'prompt.json';

    public function handle(array $data): void
    {
        if (!$chatId = $data['message']['chat']['id'] ?? null) {
            $this->logger->error('Не установлен ID чата. Прерываем.');
            return;
        }

        if (!$message = $data['message']['text'] ?? null) {
            $this->logger->error('Не передано сообщение. Прерываем.');
            return;
        }

        $newPrompt = trim(str_replace(self::COMMAND_NAME, '', $message));
        if (!json_validate($newPrompt)) {

            $errorMessage = self::errorMessage($newPrompt);

            print_r($errorMessage);

            $this->client->request(
                SendMessageDto::init($chatId, $errorMessage, ['parse_mode' => 'MarkdownV2'])
            );

            return;
        }

        $promptDir = __DIR__ . '/../../storage/telegram/';

        if (!is_dir($promptDir)) {
            mkdir($promptDir, 0755);
        }

        $newPromptFileName = $promptDir . self::PROMPT_FILE_NAME;

        file_put_contents($newPromptFileName, $newPrompt);
        $savedPrompt = file_get_contents($newPromptFileName);

        $this->client->request(
            SendMessageDto::init($chatId, self::successMessage($savedPrompt), ['parse_mode' => 'MarkdownV2'])
        );
    }

    private static function errorMessage(string $newPrompt): string
    {
        return <<< EOT
Передан невалидный *json*, попробуйте снова, Ваш prompt:
```json
$newPrompt
```
EOT;
    }

    private static function successMessage(string $newPrompt): string
    {
        return <<<EOT
Prompt *успешно* обновлен:
```json
$newPrompt
```
EOT;
    }
}
