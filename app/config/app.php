<?php

use Dflydev\Provider\DoctrineOrm\DoctrineOrmServiceProvider;
use Pixie\Application;

// Create new app
$app = new Application();

// Standard definitions
$app['app.root'] = __DIR__;
$app['app.src'] = __DIR__ . '/src';
$app['app.var'] = __DIR__ . '/var';
$app['app.web'] = __DIR__ . '/web';

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
    'annot.controllerNamespace' => 'Pixie\\Controller\\'
));

// Doctrine DBAL
$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'db.options' => array(
        'driver'   => 'pdo_sqlite',
        'path'     => $app['app.var'] . '/pixie.db',
    ),
));

// Doctrine ORM
$app->register(new DoctrineOrmServiceProvider, array(
    'orm.proxies_dir' => $app['app.var'] . '/doctrine/proxies',
    'orm.em.options' => [
        'mappings' => [
            [
                'type' => 'annotation',
                'namespace' => 'Pixie\Entities',
                'path' => $app['app.src'] . '/Entities',
            ]
        ],
    ],
));

return $app;
