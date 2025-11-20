<?php

namespace App\Services\HttpClient\Dto;

interface HttpClientDtoInterface
{
    public function getMethod(): string;
    public function getParams(): array;
    public function getApiMethod(): string;
}
