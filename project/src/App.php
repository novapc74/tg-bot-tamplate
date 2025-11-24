<?php

namespace App;

use App\Middleware\CsrfMiddleware;
use Exception;
use Throwable;
use App\Views\View;
use Psr\Log\LoggerInterface;
use App\Middleware\AuthMiddleware;
use App\Services\Request\TgRequestInterface;

final class App
{
    private array $handlers = [];

    public function __construct(
        private readonly LoggerInterface    $logger,
        private readonly TgRequestInterface $request
    )
    {
    }

    /**
     * @throws Exception
     */
    public function middleware(): void
    {
        AuthMiddleware::authenticate($this->request);
        CsrfMiddleware::csrf();
    }

    public function get(string $route, callable $callback): void
    {
        $this->append('GET', $route, $callback);
    }

    public function post(string $route, callable $callback): void
    {
        $this->append('POST', $route, $callback);
    }

    private function append(string $method, string $route, callable $callback): void
    {
        $this->handlers[] = [$method, $route, $callback];
    }

    /**
     * @throws Exception
     */
    public function run(): void
    {
        $requestUri = $_SERVER['REQUEST_URI'] ?? '/';
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

        $uri = parse_url($requestUri, PHP_URL_PATH) ?: '/';

        foreach ($this->handlers as $handler) {
            [$handlerMethod, $handlerUri, $callback] = $handler;

            if ($method !== $handlerMethod) {
                continue;
            }

            $pattern = $this->buildRegex($handlerUri);
            if (preg_match($pattern, $uri, $matches)) {
                $uriParams = $this->extractParams($handlerUri, $matches);

                try {
                    echo $callback($this->request, $uriParams);
                    $this->logger->info(sprintf('Route matched: %s %s with params %s', $method, $uri, json_encode($uriParams)));
                    exit(0);
                } catch (Throwable $e) {
                    http_response_code(500);
                    $this->logger->error(sprintf('Callback error: %s', $e->getMessage()));

                    echo (new View())
                        ->render('pages/error/_404.php', [
                            'error' => 'Internal Server Error',
                            'meta_title' => 'Page 500',
                            'code' => '500',
                        ]);

                    exit(1);
                }
            }
        }

        $hasRouteButWrongMethod = false;
        foreach ($this->handlers as $handler) {
            [$handlerMethod, $handlerUri] = array_slice($handler, 0, 2);
            if ($this->matchesRoute($handlerUri, $uri) && $method !== $handlerMethod) {
                $hasRouteButWrongMethod = true;
                break;
            }
        }

        $status = $hasRouteButWrongMethod ? 405 : 404;
        http_response_code($status);

        $message = $hasRouteButWrongMethod ? 'Method Not Allowed' : ' Page not Found';
        $this->logger->warning(
            sprintf('Invalid request: uri=%s, method=%s (%s)', $uri, $method, $message)
        );

        header('Content-Type: text/html; charset=utf-8');

        echo (new View())
            ->render('pages/error/_404.php', [
                'error' => $message,
                'meta_title' => 'Page 404',
                'code' => '404',
            ]);
    }

    // Преобразует роут с {param} в regex
    private function buildRegex(string $route): string
    {
        // Заменяем {param} на группу захвата ([^/]+)
        $pattern = preg_replace('/\{([^}]+)\}/', '([^/]+)', $route);
        return '#^' . $pattern . '$#';
    }

    // Извлекает параметры из matches по именам плейсхолдеров
    private function extractParams(string $route, array $matches): array
    {
        $params = [];
        // Находим все плейсхолдеры в роуте
        if (preg_match_all('/\{([^}]+)\}/', $route, $placeholders)) {
            foreach ($placeholders[1] as $index => $key) {
                $params[$key] = $matches[$index + 1] ?? null;  // +1 потому что matches[0] — полный матчинг
            }
        }
        return $params;
    }

    // Проверяет, матчит ли роут URI (для 405, без извлечения параметров)
    private function matchesRoute(string $route, string $uri): bool
    {
        $pattern = $this->buildRegex($route);
        return (bool)preg_match($pattern, $uri);
    }
}
