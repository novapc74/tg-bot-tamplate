<?php

namespace App\Middleware;

use App\Services\Request\TgRequestInterface;

class AuthMiddleware
{
    public static function authenticate(TgRequestInterface $request): void
    {
        if (str_contains($_SERVER['REQUEST_URI'], '/admin')) {
            if (!$request->authenticate()) {
                header("Location: /login");
                exit;
            }
        }
    }
}
