<?php

use App\Application;

// Create new app
$app = new Application();

// Core silex providers
$app->register(new Silex\Provider\ServiceControllerServiceProvider());
$app->register(new Silex\Provider\RoutingServiceProvider());
$app->register(new Silex\Provider\SessionServiceProvider());
$app->register(new Silex\Provider\HttpFragmentServiceProvider());
$app->register(new Silex\Provider\MonologServiceProvider(), [
    'monolog.logfile' => __DIR__ . '/../../var/log/application.log'
]);

// Twig
$app->register(new Silex\Provider\TwigServiceProvider(), [
    'twig.path' => __DIR__ . '/../views'
]);

// Controller annotations
$app->register(new DDesrosiers\SilexAnnotations\AnnotationServiceProvider(), array(
    'annot.cache' => new Doctrine\Common\Cache\FilesystemCache(
        __DIR__ . '/../../var/cache/annotations'
    ),
    'annot.controllerDir' => __DIR__ . '/../../src/Controller',
    'annot.controllerNamespace' => 'App\\Controller\\'
));

// Spot2
// $app->register(new \Ronanchilvers\Silex\Provider\Spot2ServiceProvider(), [
//     'spot2.connections' => [
//         'default' => 'sqlite://' . __DIR__ . '/../../var/data/database.sqlite'
//     ]
// ]);

return $app;
