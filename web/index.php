<?php
namespace Pixie;

require(__DIR__ . '/../src/bootstrap.php');

use Pixie\Web\Application;

$app = new Application();
$app->get('/testing', function() use ($app) {
    $app->render(200, 'hallo');
});

$app->run();
