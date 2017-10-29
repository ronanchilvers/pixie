<?php
require_once(__DIR__ . '/../vendor/autoload.php');

use App\Application;

if (php_sapi_name() == "cli-server") {
    $path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
    if ('/' !== $path && file_exists(__DIR__ . $path)) {
        return false;
    }
}

$app = include(__DIR__ . '/../app/config/app.php');
include(__DIR__ . '/../app/config/dev.php');
$app->run();
