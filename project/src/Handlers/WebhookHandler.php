<?php

namespace App\Handlers;

use App\Handlers\CommandHandlers\HelpCommandHandler;
use App\Handlers\CommandHandlers\ReportCommandHandler;
use App\Handlers\CommandHandlers\ShowPromptCommandHandler;
use App\Handlers\CommandHandlers\StartCommandHandler;
use Psr\Log\LoggerInterface;

final readonly class WebhookHandler
{
    public function __construct(private LoggerInterface $logger)
    {
    }

    public function handle(PayloadMessageInterface $dto): void
    {
        $text = $dto->getText();
        $handler = match ($text) {
            HelpCommandHandler::COMMAND_NAME => HelpCommandHandler::class,
            ShowPromptCommandHandler::COMMAND_NAME => ShowPromptCommandHandler::class,
            StartCommandHandler::COMMAND_NAME => StartCommandHandler::class,
            ReportCommandHandler::COMMAND_NAME => ReportCommandHandler::class,
            default => DefaultCommandHandler::class
        };

        $this->logger->info(sprintf('Handler received: %s, text: %s', $handler, $text));

        $handler->handle($dto);

    }

}
