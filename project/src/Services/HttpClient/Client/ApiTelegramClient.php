<?php

namespace App\Services\HttpClient\Client;

final class ApiTelegramClient extends AbstractHttpClient
{
    protected function auth(): void
    {
        $this->url = "https://api.telegram.org/bot$this->token";
    }
}
