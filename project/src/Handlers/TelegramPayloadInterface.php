<?php

namespace App\Handlers;

use App\Model\Chat;

interface TelegramPayloadInterface
{
    public function getText(): ?string;

    public function getChat(): Chat;
}
