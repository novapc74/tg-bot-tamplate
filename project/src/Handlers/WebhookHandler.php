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
        $handler = match ($dto->getText()) {
            HelpCommandHandler::COMMAND_NAME => HelpCommandHandler::class,
            ShowPromptCommandHandler::COMMAND_NAME => ShowPromptCommandHandler::class,
            StartCommandHandler::COMMAND_NAME => StartCommandHandler::class,
            ReportCommandHandler::COMMAND_NAME => ReportCommandHandler::class,
            default => DefaultCommandHandler::class
        };

        $handler->handle($dto);

    }

}
