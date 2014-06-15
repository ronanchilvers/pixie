<?php
namespace Pixie;

require(__DIR__ . '/../src/bootstrap.php');

use Pixie\Web\Application;

$app = new Application();
$app->run();
