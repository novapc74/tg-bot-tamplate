<?php

namespace App\Handlers;

use App\Model\Chat;

interface PayloadMessageInterface
{
    public function getText(): ?string;
    public function getChatId(): ?string;
    public function getChat(): Chat;
}
