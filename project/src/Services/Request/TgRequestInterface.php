<?php

namespace App\Services\Request;

interface TgRequestInterface
{
    public function getQuery(): ?array;

    public function getPayload(): ?array;

    public function getFormData(): ?array;

    public function getFiles(): ?array;

    public function getFile(): ?UploadFileInterface;

    public function authenticate(): bool;
}
