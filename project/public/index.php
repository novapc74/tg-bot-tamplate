<?php
declare(strict_types=1);

use App\App;
use App\Views\View;
use App\Enum\FileHelper;
use Psr\Log\LoggerInterface;
use App\Handlers\PayloadDto;
use App\Handlers\WebhookHandler;
use App\Services\Request\TgRequestInterface;
use App\Services\Request\UploadFileInterface;

session_start();
header('Content-Type: text/html; charset=utf-8');

if (!file_exists($bootstrap = __DIR__ . '/../config/bootstrap.php')) {
    http_response_code(422);
    file_put_contents('php://output', json_encode([
        'success' => false,
        'error' => 'Bootstrap file not found.',
    ]));
    exit(1);
}

require_once $bootstrap;

global $container;

/** @var App $app */
$app = $container->get(App::class);

try {
    $app->middleware();
} catch (Exception $e) {
}

$app->get('/', function () {
    return View::init()
        ->render('pages/home/index.php');
});

$app->post('/webhook-endpoint', function (TgRequestInterface $request) use ($container) {
    header('Content-Type: application/json; charset=utf-8');

    /** @var LoggerInterface $logger */
    $logger = $container->get('webhook-endpoint');
    $token = $container->get('telegram-webhook-token');
    header('Content-Type: application/json; charset=utf-8');
    http_response_code(200);

    /** check webhook token */
    if (!$authToken = getallheaders()['X-Telegram-Bot-Api-Secret-Token'] ?? null) {
        $logger->error('No authorization token provided.');
        return json_encode(['ok' => false]);
    }

    /** check is body empty */
    if (!$body = $request->getPayload()) {
        $logger->error('Webhook endpoint missing payload');
        return json_encode(['ok' => false]);
    }

    /** validate authorization token */
    if ($token !== $authToken) {
        $logger->error(sprintf('Unprocessable token %s, payload: %s', $authToken, $request->getPayload()));
        return json_encode(['ok' => false]);
    }

    $logger->info(json_encode($body, JSON_UNESCAPED_UNICODE));

    /** @var WebhookHandler $webhookHandler */
    $webhookHandler = $container->get(WebhookHandler::class);
    $webhookHandler->handle(PayloadDto::init($body));

    return json_encode(['ok' => true]);
});

$app->get('/login', function () use ($container) {
    header('Content-Type: text/html; charset=utf-8');
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

    return View::init()
        ->render('pages/admin/_login.php', [
            'meta_title' => 'Login',
            'csrf_token' => $_SESSION['csrf_token'],
        ]);
});

$app->get('/logout', function (TgRequestInterface $request) {
    $request->logout();
});

$app->post('/auth', function (TgRequestInterface $request) {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {

        return View::init()
            ->render('pages/error/_404.php', [
                'code' => 400,
                'error' => 'invalid csrf token.',
                'meta_title' => 'Error page',
            ]);
    }

    /** refresh token */
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

    if ($request->login()) {
        header('Location: admin');
        exit();
    }

    header('Location: login');
    exit();
});

$app->get('/admin', function () {
    $message = $_SESSION['FLASH'] ?? null;
    $_SESSION['FLASH'] = false;

    return View::init()
        ->render('pages/admin/_index.php', [
            'meta_title' => 'Admin panel',
            'flash' => $message
        ]);
});

$app->get('/admin/help/create', function () {
    return View::init()->render('pages/help/_upload.php', [
        'csrf_token' => $_SESSION['csrf_token'],
        'meta_title' => 'Upload manual',
    ]);
});

#TODO убрать дублирование, и переписать более локанично :)
$app->post('/admin/prompt/upload', function (TgRequestInterface $request) {
    /** @var UploadFileInterface $file */
    if (!$file = $request->getFile() ?? null) {
        http_response_code(400);
        return View::init()
            ->render('pages/error/_404.php', [
                'code' => 400,
                'error' => 'Файл не загрузился.',
                'meta_title' => 'Error page',
            ]);
    }

    $fileDir = FileHelper::FILE_DIR->value;
    $fileName = FileHelper::PROMPT->value;

    if (!is_dir($fileDir)) {
        mkdir($fileDir, 0755, true);
    }

    if ($file->error() !== UPLOAD_ERR_OK) {
        http_response_code(422);
        return View::init()
            ->render('pages/error/_404.php', [
                'code' => 400,
                'error' => 'Ошибка загрузки файла: ' . $file->name(),
                'meta_title' => 'Error page',
            ]);
    }

    if ($file->size() > 2 * 1024 * 1024) {  // 2MB
        http_response_code(422);
        return View::init()
            ->render('pages/error/_404.php', [
                'code' => 400,
                'error' => 'Файл слишком большой:' . $file->name(),
                'meta_title' => 'Error page',
            ]);
    }

    $fileContent = file_get_contents($file->tmp_name());
    if (!json_validate($fileContent)) {
        http_response_code(422);
        return View::init()
            ->render('pages/error/_404.php', [
                'code' => 400,
                'error' => 'Файл содержит невалидный json: ' . $file->name(),
                'meta_title' => 'Error page',
            ]);
    }

    if (file_put_contents(FileHelper::PROMPT_FILE_PATH->value, $fileContent)) {
        header('Location: /admin');
        $_SESSION['FLASH'] = 'Файл успешно сохранен на сервере.';
        exit();
    }

    http_response_code(422);
    return View::init()
        ->render('pages/error/_404.php', [
            'code' => 400,
            'error' => 'Ошибка записи файла на сервер: ' . $file->name(),
            'meta_title' => 'Error page',
        ]);
});

$app->get('/admin/prompt/create', function () {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

    return View::init()
        ->render('pages/prompt/_upload.php', [
            'csrf_token' => $_SESSION['csrf_token'],
            'meta_title' => 'Upload prompt',
        ]);
});

$app->get('/admin/prompt/show', function () {
    $file = FileHelper::PROMPT_FILE_PATH->value;

    if (is_file($file)) {
        $content = file_get_contents($file);

        return View::init()
            ->render('pages/prompt/_show.php', [
                'file_content' => $content,
                'meta_title' => 'Prompt'
            ]);
    }

    return View::init()
        ->render('pages/error/_404.php', [
            'code' => '422',
            'meta_title' => 'Error page',
            'error' => 'Файл "prompt" не найден.'
        ]);
});

$app->get('/admin/prompt/download', function () {
    $file = __DIR__ . '/../storage/telegram/prompt.json';

    if (is_file($file)) {
        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename="prompt.json"');
        header('Content-Length: ' . filesize($file));
        header('Cache-Control: must-revalidate');
        header('Pragma: public');

        ob_clean();
        flush();

        return readfile($file);
    }

    return View::init()
        ->render('pages/error/_404.php', [
            'code' => '422',
            'meta_title' => 'Error page',
            'error' => 'Файл "prompt.json" не найден.'
        ]);
});

$app->get('/admin/help/download', function () {
    $file = __DIR__ . '/../storage/telegram/manual.md';

    if (is_file($file)) {
        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename="manual.md"');
        header('Content-Length: ' . filesize($file));
        header('Cache-Control: must-revalidate');
        header('Pragma: public');

        ob_clean();
        flush();

        return readfile($file);
    }

    return View::init()
        ->render('pages/error/_404.php', [
            'code' => '422',
            'meta_title' => 'Error page',
            'error' => 'Файл "manual.md" не найден.'
        ]);
});

$app->get('/admin/manual/show', function () {
    $file = FileHelper::MANUAL_FILE_PATH->value;

    if (is_file($file)) {
        $content = file_get_contents($file);

        return View::init()
            ->render('pages/manual/_show.php', [
                'file_content' => $content,
                'meta_title' => 'Manual'
            ]);
    }

    return View::init()
        ->render('pages/error/_404.php', [
            'code' => '422',
            'meta_title' => 'Error page',
            'error' => 'Файл "help" не найден.'
        ]);
});

$app->post('/admin/help/upload', function (TgRequestInterface $request) {
    /** @var UploadFileInterface $file */
    if (!$file = $request->getFiles()[0] ?? null) {
        http_response_code(400);
        return View::init()
            ->render('pages/error/_404.php', [
                'code' => 400,
                'error' => 'Файл не загрузился.',
                'meta_title' => 'Error page',
            ]);
    }

    $fileDir = FileHelper::FILE_DIR->value;
    $fileName = FileHelper::MANUAL->value;

    if (!is_dir($fileDir)) {
        mkdir($fileDir, 0755, true);
    }

    if ($file->error() !== UPLOAD_ERR_OK) {
        http_response_code(422);
        return View::init()
            ->render('pages/error/_404.php', [
                'code' => 400,
                'error' => 'Ошибка загрузки файла: ' . $file->name(),
                'meta_title' => 'Error page',
            ]);
    }

    if ($file->size() > 2 * 1024 * 1024) {  // 2MB
        http_response_code(422);
        return View::init()
            ->render('pages/error/_404.php', [
                'code' => 400,
                'error' => 'Файл слишком большой:' . $file->name(),
                'meta_title' => 'Error page',
            ]);
    }

    $fileExtension = pathinfo($file->name())['extension'] ?? '';

    if ('md' !== strtolower($fileExtension)) {
        http_response_code(422);
        return View::init()
            ->render('pages/error/_404.php', [
                'code' => 400,
                'error' => 'Файл с недопустимым расширением:' . $file->name(),
                'meta_title' => 'Error page',
            ]);
    }

    $fileContent = file_get_contents($file->tmp_name());
    if (file_put_contents(FileHelper::MANUAL_FILE_PATH->value, $fileContent)) {
        header('Location: /admin');
        $_SESSION['FLASH'] = 'Файл успешно сохранен на сервере.';
        exit();
    }

    http_response_code(422);
    return View::init()
        ->render('pages/error/_404.php', [
            'code' => 400,
            'error' => 'Ошибка записи файла на сервер: ' . $file->name(),
            'meta_title' => 'Error page',
        ]);
});

try {
    $app->run();
} catch (Exception $e) {
    exit(1);
}


