<?php
ini_set('display_errors', 0);

require_once(__DIR__ . '/../vendor/autoload.php');

use App\Application;

$app = include(__DIR__ . '/../app/config/app.php');
include(__DIR__ . '/../app/config/prod.php');
$app->run();
