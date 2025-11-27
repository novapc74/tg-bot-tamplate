<?php

namespace App\Model;

use JetBrains\PhpStorm\ArrayShape;

class Chat
{
    const string PRIVATE = 'private';
    const string GROUP = 'group';
    const string SUPERGROUP = 'supergroup';
    const string CHANEL = 'channel';
    private ?string $id;
    private ?string $firstName;
    private ?string $lastName;
    private ?string $title;
    private ?string $username;
    private ?string $type;

    public function __construct(array $chatData)
    {
        $this->id = $chatData['id'] ?? null;
        $this->title = $chatData['title'] ?? null;
        $this->username = $chatData['username'] ?? null;
        $this->type = $chatData['type'] ?? null;
        $this->firstName = $chatData['first_name'] ?? null;
        $this->lastName = $chatData['last_name'] ?? null;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getType(): string
    {
        return $this->type;
    }

    #[ArrayShape([
        'private' => 'string',
        'group' => 'string',
        'supergroup' => 'string',
        'channel' => 'string',
    ])]
    public static function getAvailableTypes(): array
    {
        return [
            'private' => self::PRIVATE,
            'group' => self::GROUP,
            'supergroup' => self::SUPERGROUP,
            'channel' => self::CHANEL,
        ];
    }
}
