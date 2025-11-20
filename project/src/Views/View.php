<?php
declare(strict_types=1);

namespace App\Views;

use Exception;

class View
{
    private const string CSS_FILE = '/build/css/app.css';
    private const string JS_FILE = '/build/js/app.js';
    private const string TEMPLATES_DIR = '/project/templates/';

    private array $blocks = [];
    private array $variables = [];

    /**
     * @throws Exception
     */
    public function render($templatePath, $variables = [], $localVars = []): string
    {
        $this->variables = array_merge($variables, $localVars);

        $content = $this->loadTemplate($templatePath);
        // Сначала обрабатываем extends и blocks
        $content = $this->processExtends($content);
        $content = $this->processBlocks($content);
        // Затем includes (теперь они могут быть внутри блоков)
        $content = $this->processIncludes($content);

        $this->replaceVariables($content);
        $this->processCustomTags($content);

        return $content;
    }


    /**
     * @throws Exception
     */
    private function loadTemplate($path): ?string
    {
        $fullPath = self::TEMPLATES_DIR . $path;
        if (!file_exists($fullPath)) {
            throw new Exception("Template not found: $fullPath");
        }

        return file_get_contents($fullPath);
    }

    /**
     * @throws Exception
     */
    private function processExtends($content): string
    {
        // Ищем {% extends 'path' %} (с учётом возможных пробелов)
        if (preg_match('/\{\%\s*extends\s*\'([^\']+)\'\s*\%\}[\s\S]*/', $content, $matches)) {
            $parentPath = $matches[1];
            $parentContent = $this->loadTemplate($parentPath);

            // Извлекаем блоки из дочернего шаблона
            $this->extractBlocks($content);

            // Возвращаем родительский шаблон для дальнейшей обработки
            return $parentContent;
        }

        return $content;
    }

    private function extractBlocks(string $content): void
    {
        // Ищем все {% block name %}{% end block %} (с учётом возможных пробелов)
        preg_match_all('/\{\%\s*block\s+(\w+)\s*\%\}([\s\S]*?)\{\%\s*end\s+block\s*\%\}/', $content, $matches);
        foreach ($matches[1] as $index => $blockName) {
            $this->blocks[$blockName] = $matches[2][$index];
        }
    }

    private function processIncludes(string &$content): string
    {
        // Обрабатываем {% include 'path' %} или {% include 'path' with {"key": "value"} %} (с учётом кавычек и пробелов)
        $content = preg_replace_callback('/\{\%\s*include\s*([\"\'])([^\"\']+)\1\s*(?:with\s*(\{.*?\})\s*)?\%\}/', function ($matches) {
            $includePath = $matches[2]; // Путь без кавычек
            $withClause = $matches[3] ?? ''; // Всё внутри {}

            $withVars = [];
            if (!empty($withClause)) {
                // Пытаемся парсить как JSON (требует {"key": "value"})
                $withVars = json_decode($withClause, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    // Fallback: если не JSON, выбрасываем ошибку или игнорируем (для простоты — выбрасываем)
                    throw new Exception("Invalid with clause in include: $withClause. Use valid JSON like {\"key\": \"value\"}.");
                }
            }

            // Проверка на рекурсию (простая: если путь уже в стеке, пропустить)
            static $includeStack = [];
            if (in_array($includePath, $includeStack)) {
                throw new Exception("Recursive include detected: $includePath");
            }
            $includeStack[] = $includePath;

            try {
                // Рекурсивно рендерим include с локальными переменными
                $result = $this->render($includePath, $this->variables, $withVars);
            } finally {
                // Убираем из стека после обработки
                array_pop($includeStack);
            }

            return $result;
        }, $content);

        return $content;
    }


    private function processBlocks($content): string
    {
        // Заменяем {% block name %} на содержимое блока (с учётом возможных пробелов)
        return preg_replace_callback('/\{\%\s*block\s+(\w+)\s*\%\}([\s\S]*?)\{\%\s*end\s+block\s*\%\}/', function ($matches) {
            $blockName = $matches[1];
            return $this->blocks[$blockName] ?? $matches[2];
        }, $content);
    }

    /**
     * Замена {{ variable }} на реальные данные (с учётом возможных пробелов вокруг переменной)
     *
     * @param string $content
     * @return void
     */
    private function replaceVariables(string &$content): void
    {
        foreach ($this->variables as $key => $value) {
            // Используем preg_replace для гибкой замены {{variable}}, {{ variable }}, {{ variable}}, etc.
            $content = preg_replace('/\{\{\s*' . preg_quote($key, '/') . '\s*\}\}/', htmlspecialchars((string)$value), $content);
        }
    }

    /**
     * Подстановка файлов в {% app_css %} и {% app_js %} (с учётом возможных пробелов)
     *
     * @param string $content
     * @return void
     */
    private function processCustomTags(string &$content): void
    {
        $content = preg_replace('/\{\%\s*app_js\s*\%\}/', '<script type="module" src="' . self::JS_FILE . '"></script>', $content);
        $content = preg_replace('/\{\%\s*app_css\s*\%\}/', '<link rel="stylesheet" href="' . self::CSS_FILE . '">', $content);
    }
}
