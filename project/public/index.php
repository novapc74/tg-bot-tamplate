<?php
declare(strict_types=1);

use App\App;
use App\Views\View;
use App\Services\Request\TgRequestInterface;
use App\Services\Request\UploadFileInterface;

header('Content-Type: application/json; charset=utf-8');

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

$app->post('/webhook-endpoint', function (TgRequestInterface $request, array $uriParams) {

    if ($data = $request->getPayload()) {
        #TODO WebhookHandler::init()->handle($data);
    }

    http_response_code(200);
    header('Content-Type: application/json; charset=utf-8');

    return json_encode(['ok' => true, 'result' => true]);
});

$app->get('/admin/{id}/user/{item}', function (TgRequestInterface $request, array $uriParams) {

    $id = $uriParams['id'];
    $item = $uriParams['item'];
    $queryParams = $request->getQuery();

    return json_encode(compact('id', 'item', 'queryParams'));
});

$app->get('/admin', function (TgRequestInterface $request, array $uriParams) {
    header('Content-Type: text/html; charset=utf-8');

    return (new View())
        ->render('pages/admin/_index.php', [
            'meta_title' => 'Admin panel',
            'test' => 10
        ]);
});

$app->get('/admin/prompt/create', function (TgRequestInterface $request, array $uriParams) {
    header('Content-Type: text/html; charset=utf-8');

    session_start();
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

    return (new View())
        ->render('pages/prompt/_upload.php', [
            'csrf_token' => $_SESSION['csrf_token'],
            'meta_title' => 'Upload prompt',
        ]);
});

$app->post('/admin/prompt/upload', function (TgRequestInterface $request, array $uriParams) {
    header('Content-Type: text/html; charset=utf-8');

    session_start();
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        #TODO вернуть страницу ошибки ...
        die('Обнаружена CSRF атака!');
    }

    /** refresh token */
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

    /** @var UploadFileInterface $file */
    if (!$file = $request->getFiles()[0] ?? null) {
        http_response_code(400);
        return json_encode(['success' => false, 'error' => 'No files uploaded.']);
    }

    $fileDir = __DIR__ . '/../storage/telegram/';

    if (!is_dir($fileDir)) {
        mkdir($fileDir, 0755, true);
    }

    if ($file->error() !== UPLOAD_ERR_OK) {
        http_response_code(400);
        return json_encode(['success' => false, 'error' => 'Upload error for file: ' . $file->name()]);
    }

    if ($file->size() > 2 * 1024 * 1024) {  // 2MB
        return json_encode(['success' => false, 'error' => 'File too large: ' . $file->name()]);
    }

    $fileContent = file_get_contents($file->tmp_name());
    if (!json_validate($fileContent)) {
        return json_encode(['success' => false, 'error' => 'File contains invalid json']);
    }

    $targetPath = $fileDir . 'prompt.json';
    if (file_put_contents($targetPath, $fileContent)) {
        return json_encode([
            'success' => true,
            'token' => $_SESSION['csrf_token']
        ]);
    }

    return json_encode(['success' => false, 'error' => 'Failed to save: ' . $file->name()]);
});

$app->run();

