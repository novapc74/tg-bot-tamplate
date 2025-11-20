<?php

namespace App\Services\Request;

interface UploadFileInterface
{
    public function name(): string;

    public function type(): string;

    public function size(): int;

    public function error(): int;

    public function full_path(): string;

    public function tmp_name(): string;
}
