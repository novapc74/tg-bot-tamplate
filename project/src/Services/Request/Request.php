<?php

namespace App\Services\Request;

use InvalidArgumentException;
use App\Services\Singleton\AbstractSingleton;

class Request extends AbstractSingleton implements TgRequestInterface
{
    private ?array $payload = null;
    private ?array $query = null;
    private ?array $formData = null;
    private ?array $files = null;

    private function payload(): void
    {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        if (stripos($contentType, 'application/json') === false) {
            return;
        }

        $input = file_get_contents('php://input');
        if (!$input || strlen($input) > 1048576) { // Лимит 1MB
            return;
        }

        $payload = json_decode($input, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new InvalidArgumentException('Invalid JSON');
        }

        $this->payload = $payload;
    }

    private function query(): void
    {
        if (empty($queryString = $_SERVER['QUERY_STRING'])) {
            return;
        }

        parse_str($queryString, $params);
        $this->query = $params;
    }

    private function formData(): void
    {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        if (!preg_match('/^(application\/x-www-form-urlencoded|multipart\/form-data)/i', $contentType)) {
            return;
        }

        if (empty($_POST)) {
            return;
        }

        $sanitized = [];
        foreach ($_POST as $key => $value) {
            $sanitized[$key] = is_array($value) ? array_map('htmlspecialchars', $value) : htmlspecialchars($value);
        }

        $this->formData = $sanitized;
    }

    /**
     * Нормализует $_FILES в массив объектов UploadFileInterface.
     * Обрабатывает множественные файлы и применяет sanitization к 'name'.
     */
    private function files(): void
    {
        if (empty($_FILES)) {
            return;
        }

        $normalizeFiles = function ($files) {
            $result = [];
            foreach ($files as $file) {
                if (is_array($file['name'])) {
                    foreach ($file['name'] as $index => $name) {
                        $result[] = [
                            'name' => htmlspecialchars($name, ENT_QUOTES, 'UTF-8'),
                            'type' => $file['type'][$index] ?? '',
                            'size' => $file['size'][$index] ?? 0,
                            'error' => $file['error'][$index] ?? UPLOAD_ERR_NO_FILE,
                            'tmp_name' => $file['tmp_name'][$index] ?? '',
                            'full_path' => $file['full_path'][$index] ?? '',
                        ];
                    }
                } else {
                    $result[] = [
                        'name' => htmlspecialchars($file['name'], ENT_QUOTES, 'UTF-8'),
                        'type' => $file['type'] ?? '',
                        'size' => $file['size'] ?? 0,
                        'error' => $file['error'] ?? UPLOAD_ERR_NO_FILE,
                        'tmp_name' => $file['tmp_name'] ?? '',
                        'full_path' => $file['full_path'] ?? '',
                    ];
                }
            }

            return $result;
        };

        $normalized = $normalizeFiles($_FILES);
        foreach ($normalized as $fileData) {
            $this->files[] = new UploadFile($fileData);
        }
    }


    public function getQuery(): ?array
    {
        if ($this->query === null) {
            $this->query();
        }

        return $this->query;
    }

    public function getPayload(): ?array
    {
        if ($this->payload === null) {
            $this->payload();
        }

        return $this->payload;
    }

    public function getFormData(): ?array
    {
        if ($this->formData === null) {
            $this->formData();
        }

        return $this->formData;
    }

    /**
     * @return UploadFileInterface[]
     */
    public function getFiles(): array
    {
        if ($this->files === null) {
            $this->files();
        }

        return $this->files;
    }
}
