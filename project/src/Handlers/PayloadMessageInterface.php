<?php

namespace App\Handlers;

interface PayloadMessageInterface
{
    public function getText(): ?string;
    public function getChatId(): ?string;

}
