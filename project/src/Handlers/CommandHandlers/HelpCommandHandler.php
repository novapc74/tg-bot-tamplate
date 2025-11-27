<?php

namespace App\Handlers\CommandHandlers;

use App\Handlers\AbstractTelegramBotHandler;
use App\Handlers\TelegramPayloadInterface;
use App\Services\HttpClient\Dto\SendMessageDto;

final readonly class HelpCommandHandler extends AbstractTelegramBotHandler
{
    private const string HELP_MESSAGE = '<b>Инструкция пользования чатом:</b> <span class="tg-spoiler" style="color: red">(в разработке)</span>. <tg-emoji emoji-id="5368324170671202286">👍</tg-emoji><pre>pre-formatted fixed-width code block</pre>';
    const string COMMAND_NAME = '/manual';

    public function handle(TelegramPayloadInterface $dto): void
    {
        if (!$chatId = $dto->getChat()?->getId()) {
            $this->logger->error('Не установлен ID чата. Прерываем. тело ответа');
            return;
        }

        $text = self::HELP_MESSAGE;
        $options = [
            'parse_mode' => 'HTML',
        ];

        $helpFile = __DIR__ . '/../../../storage/telegram/help_command.md';
        if (file_exists($helpFile)) {
            $text = file_get_contents($helpFile);
            $options = [
                'parse_mode' => 'MarkdownV2',
            ];
        }

        $this->client->request(
            SendMessageDto::init($chatId, $text, $options)
        );
    }
}
