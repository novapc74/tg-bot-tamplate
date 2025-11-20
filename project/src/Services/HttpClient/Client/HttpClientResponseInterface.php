<?php

namespace App\Services\HttpClient\Client;

interface HttpClientResponseInterface
{
    public function getData(): ?array;
}
