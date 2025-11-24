<?php

namespace App\Services\Request;

interface AuthInterface
{
    public function authenticate(): bool;
    public function login(): bool;
}
