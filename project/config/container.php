<?php

use App\App;
use Monolog\Level;
use Monolog\Logger;
use DI\ContainerBuilder;
use App\Services\Request\Request;
use Psr\Container\ContainerInterface;
use App\Handlers\DefaultCommandHandler;
use Monolog\Handler\RotatingFileHandler;
use Symfony\Component\HttpClient\HttpClient;
use App\Handlers\CommandHandlers\HelpCommandHandler;
use App\Services\HttpClient\Client\ApiTelegramClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use App\Handlers\CommandHandlers\StartCommandHandler;
use App\Handlers\CommandHandlers\ReportCommandHandler;
use App\Services\HttpClient\Client\ApiOpenRouterClient;
use App\Handlers\CommandHandlers\ShowPromptCommandHandler;
use App\Handlers\CommandHandlers\UpdatePromptCommandHandler;

return function () {

    $definitions = [

        'app-logger' => function () {
            $logger = new Logger('telegram-client');
            $logger->pushHandler(
                new RotatingFileHandler(__DIR__ . '/../var/log/app-logger/app-logger.log',
                    10,
                    Level::Debug
                ));
            return $logger;
        },

        'telegram-webhook-token' => function () {
            return $_ENV['TELEGRAM_WEBHOOK_TOKEN'] ?? throw new Exception('TELEGRAM_WEBHOOK_TOKEN not set');
        },

        'telegram-bot-url' => function () {
            return $_ENV['TELEGRAM_BOT_URL'] ?? throw new Exception('TELEGRAM_BOT_URL not set');
        },

        'telegram-bot-token' => function () {
            return $_ENV['TELEGRAM_BOT_TOKEN'] ?? throw new Exception('TELEGRAM_BOT_TOKEN not set');
        },

        'open-router-url' => function () {
            return $_ENV['OPEN_ROUTER_URL'] ?? throw new Exception('OPEN_ROUTER_URL not set');
        },

        'open-router-token' => function () {
            return $_ENV['OPEN_ROUTER_TOKEN'] ?? throw new Exception('OPEN_ROUTER_TOKEN not set');
        },

        'telegram-client' => function () {
            $logger = new Logger('telegram-client');
            $logger->pushHandler(
                new RotatingFileHandler(__DIR__ . '/../var/log/telegram-client/telegram-client.log',
                    10,
                    Level::Debug
                ));
            return $logger;
        },

        'help-handler' => function () {
            $logger = new Logger('help-handler');
            $logger->pushHandler(
                new RotatingFileHandler(__DIR__ . '/../var/log/help-handler/help-handler.log',
                    10,
                    Level::Debug
                ));
            return $logger;
        },

        'start-handler' => function () {
            $logger = new Logger('start-handler');
            $logger->pushHandler(
                new RotatingFileHandler(__DIR__ . '/../var/log/start-handler/start-handler.log',
                    10,
                    Level::Debug
                ));
            return $logger;
        },

        'default-handler' => function () {
            $logger = new Logger('default-handler');
            $logger->pushHandler(
                new RotatingFileHandler(__DIR__ . '/../var/log/default-handler/default-handler.log',
                    10,
                    Level::Debug
                ));
            return $logger;
        },

        'prompt-handler' => function () {
            $logger = new Logger('prompt-handler');
            $logger->pushHandler(
                new RotatingFileHandler(__DIR__ . '/../var/log/prompt-handler/prompt-handler.log',
                    10,
                    Level::Debug
                ));
            return $logger;
        },

        'report-handler' => function () {
            $logger = new Logger('report-handler');
            $logger->pushHandler(
                new RotatingFileHandler(__DIR__ . '/../var/log/report-handler/report-handler.log',
                    10,
                    Level::Debug
                ));
            return $logger;
        },

        'open-router-client' => function () {
            $logger = new Logger('open-router-client');
            $logger->pushHandler(
                new RotatingFileHandler(__DIR__ . '/../var/log/open-router-client/open-router-client.log',
                    10,
                    Level::Debug
                ));
            return $logger;
        },

        HttpClientInterface::class => function () {
            return HttpClient::create();
        },

        ApiTelegramClient::class => function (ContainerInterface $container) {
            return new ApiTelegramClient(
                $container->get(HttpClientInterface::class),
                $container->get('telegram-bot-url'),
                $container->get('telegram-bot-token'),
                $container->get('telegram-client')
            );
        },

        ApiOpenRouterClient::class => function (ContainerInterface $container) {
            return new ApiOpenRouterClient(
                $container->get(HttpClientInterface::class),
                $container->get('open-router-url'),
                $container->get('open-router-token'),
                $container->get('open-router-client')
            );
        },

        StartCommandHandler::class => function (ContainerInterface $container) {
            return new StartCommandHandler(
                $container->get(ApiTelegramClient::class),
                $container->get('start-handler')
            );
        },

        HelpCommandHandler::class => function (ContainerInterface $container) {
            return new HelpCommandHandler(
                $container->get(ApiTelegramClient::class),
                $container->get('help-handler')
            );
        },

        DefaultCommandHandler::class => function (ContainerInterface $container) {
            return new DefaultCommandHandler(
                $container->get(ApiOpenRouterClient::class),
                $container->get('default-handler'),
                $container->get(ApiTelegramClient::class)
            );
        },

        UpdatePromptCommandHandler::class => function (ContainerInterface $container) {
            return new UpdatePromptCommandHandler(
                $container->get(ApiTelegramClient::class),
                $container->get('prompt-handler')
            );
        },

        ShowPromptCommandHandler::class => function (ContainerInterface $container) {
            return new ShowPromptCommandHandler(
                $container->get(ApiTelegramClient::class),
                $container->get('prompt-handler')
            );
        },

        ReportCommandHandler::class => function (ContainerInterface $container) {
            return new ReportCommandHandler(
                $container->get(ApiTelegramClient::class),
                $container->get('report-handler')
            );
        },

        Request::class => function () {
            return Request::getInstance();
        },

        App::class => function (ContainerInterface $container) {
            return new App(
                $container->get('app-logger'),
                $container->get(Request::class),
            );
        }
    ];

    return (new ContainerBuilder())
        ->addDefinitions($definitions)
        ->build();
};
