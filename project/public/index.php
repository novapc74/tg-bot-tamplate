<?php
declare(strict_types=1);

use App\App;
use App\Views\View;
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

$app->middleware();

$app->get('/', function (TgRequestInterface $request, array $uriParams) {
    header('Location: admin');
    exit();
});

$app->post('/webhook-endpoint', function (TgRequestInterface $request, array $uriParams) use ($container) {
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

$app->get('/login', function (TgRequestInterface $request, array $uriParams) use ($container) {
    header('Content-Type: text/html; charset=utf-8');
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

    return (new View())
        ->render('pages/admin/_login.php', [
            'meta_title' => 'Login',
            'csrf_token' => $_SESSION['csrf_token'],
        ]);
});

$app->get('/logout', function (TgRequestInterface $request, array $uriParams) {
    $request->logout();
});

$app->post('/auth', function (TgRequestInterface $request, array $uriParams) {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('Обнаружена CSRF атака!');
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


$app->get('/admin', function (TgRequestInterface $request, array $uriParams) {

    $message = $_SESSION['FLASH'] ?? null;
    $_SESSION['FLASH'] = false;

    return (new View())
        ->render('pages/admin/_index.php', [
            'meta_title' => 'Admin panel',
            'flash' => $message
        ]);
});

$app->get('/admin/prompt/create', function (TgRequestInterface $request, array $uriParams) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

    return (new View())
        ->render('pages/prompt/_upload.php', [
            'csrf_token' => $_SESSION['csrf_token'],
            'meta_title' => 'Upload prompt',
        ]);
});

$app->post('/admin/prompt/upload', function (TgRequestInterface $request, array $uriParams) {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {

        http_response_code(403);
        return (new View())
            ->render('pages/error/_404.php', [
                'code' => 403,
                'error' => 'Обнаружена CSRF атака!'
            ]);
    }

    /** refresh token */
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

    /** @var UploadFileInterface $file */
    if (!$file = $request->getFiles()[0] ?? null) {
        http_response_code(400);
        return (new View())
            ->render('pages/error/_404.php', [
                'code' => 400,
                'error' => 'Файл не загрузился.',
                'meta_title' => 'Error page',
            ]);
    }

    $fileDir = __DIR__ . '/../storage/telegram/';

    if (!is_dir($fileDir)) {
        mkdir($fileDir, 0755, true);
    }

    if ($file->error() !== UPLOAD_ERR_OK) {
        http_response_code(422);
        return (new View())
            ->render('pages/error/_404.php', [
                'code' => 400,
                'error' => 'Ошибка загрузки файла: ' . $file->name(),
                'meta_title' => 'Error page',
            ]);
    }

    if ($file->size() > 2 * 1024 * 1024) {  // 2MB
        http_response_code(422);
        return (new View())
            ->render('pages/error/_404.php', [
                'code' => 400,
                'error' => 'Файл слишком большой:' . $file->name(),
                'meta_title' => 'Error page',
            ]);
    }

    $fileContent = file_get_contents($file->tmp_name());
    if (!json_validate($fileContent)) {
        http_response_code(422);
        return (new View())
            ->render('pages/error/_404.php', [
                'code' => 400,
                'error' => 'Файл содержит невалидный json: ' . $file->name(),
                'meta_title' => 'Error page',
            ]);
    }

    $targetPath = $fileDir . 'prompt.json';
    if (file_put_contents($targetPath, $fileContent)) {
        header('Location: /admin');
        $_SESSION['FLASH'] = 'Файл успешно сохранен на сервере.';
        exit();
    }

    http_response_code(422);
    return (new View())
        ->render('pages/error/_404.php', [
            'code' => 400,
            'error' => 'Ошибка записи файла на сервер: ' . $file->name(),
            'meta_title' => 'Error page',
        ]);
});

try {
    $app->run();
} catch (Exception $e) {
    #TODO добавить в логер...
    exit(1);
}

