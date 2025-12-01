<?php

namespace App\Services\HttpClient\Client;

use App\Services\HttpClient\Dto\HttpClientDtoInterface;

interface CustomHttpClientInterface
{
    public function request(HttpClientDtoInterface $dto): ?HttpClientResponseInterface;
}
