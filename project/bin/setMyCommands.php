#!/usr/bin/env php
<?php

use App\Services\HttpClient\Dto\SetMyCommandsDto;
use App\Services\HttpClient\Client\ApiTelegramClient;

if (!file_exists($bootstrap = __DIR__ . '/../config/bootstrap.php')) {
    exit('Bootstrap file not found.');
}

require_once $bootstrap;

global $container;

$commandFile = __DIR__ . '/../storage/telegram/commands.json';

if (!file_exists($commandFile)) {
    exit('../storage/telegram/commands.json file not found.');
}

$content = file_get_contents($commandFile);
$options = json_decode($content, true);

/**@var ApiTelegramClient $client */
$client = $container->get(ApiTelegramClient::class);
$responseData = $client->request(SetMyCommandsDto::init($options));
$data = $responseData->getData();

if ($data['ok'] ?? null === 1 && $data['result'] ?? null === 1) {
    exit("Команды переданы успешно!\n");
}
exit('Что-то пошло не так, смотрите логи "tg-api-client".');
