<?php

namespace App\Handlers;

use App\Handlers\CommandHandlers\AsicPriceFromChatHandler;
use Psr\Log\LoggerInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Container\ContainerExceptionInterface;
use App\Handlers\CommandHandlers\HelpCommandHandler;
use App\Handlers\CommandHandlers\StartCommandHandler;
use App\Handlers\CommandHandlers\ReportCommandHandler;
use App\Handlers\CommandHandlers\ShowPromptCommandHandler;
use App\Handlers\CommandHandlers\AsicPriceGeneratorHandler;

final readonly class WebhookHandler
{
    public function __construct(private LoggerInterface $logger, private ContainerInterface $container)
    {
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function handle(TelegramPayloadInterface $dto): void
    {
        $text = $dto->getText();
        if ($dto->getChat()->getId() == '-1003373031540') {
            $text = '-1003373031540';
        }

        $handler = match ($text) {
            HelpCommandHandler::COMMAND_NAME => HelpCommandHandler::class,
            ShowPromptCommandHandler::COMMAND_NAME => ShowPromptCommandHandler::class,
            StartCommandHandler::COMMAND_NAME => StartCommandHandler::class,
            ReportCommandHandler::COMMAND_NAME => ReportCommandHandler::class,
            AsicPriceGeneratorHandler::COMMAND_NAME => AsicPriceGeneratorHandler::class,
            AsicPriceFromChatHandler::COMMAND_NAME => AsicPriceFromChatHandler::class,
            default => DefaultCommandHandler::class
        };

        $this->logger->info(sprintf('Handler received: %s, text: %s', $handler, $text));

        $handler = $this->container->get($handler);
        $handler->handle($dto);
    }
}
