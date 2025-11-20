<?php

$autoloadFile = __DIR__ . '/../vendor/autoload.php';
$dotenvFile = __DIR__ . '/../config/dotenv.php';
$containerFile = __DIR__ . '/../config/container.php';

function validate(string $name, string $file): void
{
    if (!file_exists($file)) {
        throw new RuntimeException(sprintf("%s file does not exist: %s", $name, $file));
    }

    if (!is_readable($file)) {
        throw new RuntimeException(sprintf("%s file is not readable: %s", $name, $file));
    }
}

$files = [
    'Composer autoload' => $autoloadFile,
    'Dotenv' => $dotenvFile,
];

foreach ($files as $name => $file) {
    validate($name, $file);
    require_once $file;
}

validate('Container', $containerFile);

$containerFunction = require_once $containerFile;
global $container;
$container = $containerFunction();
