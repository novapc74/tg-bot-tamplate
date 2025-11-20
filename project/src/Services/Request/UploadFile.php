<?php

namespace App\Services\Request;

class UploadFile implements UploadFileInterface
{
    private string $name;
    private string $type;
    private int $size;
    private int $error;
    private string $tmp_name;
    private string $full_path;

    public function __construct(array $data)
    {
        $this->name = $data['name'];
        $this->type = $data['type'];
        $this->size = $data['size'];
        $this->error = $data['error'];
        $this->tmp_name = $data['tmp_name'];
        $this->full_path = $data['full_path'];
    }

    public function name(): string
    {
        return $this->name;
    }

    public function type(): string
    {
        return $this->type;
    }

    public function size(): int
    {
        return $this->size;
    }

    public function error(): int
    {
        return $this->error;
    }

    public function full_path(): string
    {
        return $this->full_path;
    }

    public function tmp_name(): string
    {
        return $this->tmp_name;
    }
}
