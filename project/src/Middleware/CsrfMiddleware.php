<?php

namespace App\Middleware;

use Exception;
use App\Views\View;
use Random\RandomException;

class CsrfMiddleware
{
    /**
     * @throws Exception
     */
    public static function csrf(): void
    {
        $requestUri = $_SERVER['REQUEST_URI'];

        if (str_contains($requestUri, 'upload')) {
            self::checkCsrf();
        }

        if (str_contains($requestUri, 'create')) {
            self::updateCsrf();
        }
    }

    /**
     * @throws RandomException
     */
    private static function updateCsrf(): void
    {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    /**
     * @throws Exception
     */
    private static function checkCsrf(): void
    {
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {

            http_response_code(403);
            echo (new View())
                ->render('pages/error/_404.php', [
                    'code' => 403,
                    'error' => 'Обнаружена CSRF атака!'
                ]);
        }

        /** refresh token */
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
}
