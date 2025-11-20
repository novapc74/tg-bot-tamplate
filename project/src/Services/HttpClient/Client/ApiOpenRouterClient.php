<?php

namespace App\Services\HttpClient\Client;

final class ApiOpenRouterClient extends AbstractHttpClient
{
    protected function auth(): void
    {
        $this->options['headers']['Authorization'] = 'Bearer ' . $this->token;
    }
}
