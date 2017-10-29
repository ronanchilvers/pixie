<?php

// Include the production config
require(__DIR__ . '/prod.php');

// Enable debug mode
$app['debug'] = true;

// Add web profiler
$app->register(new Silex\Provider\WebProfilerServiceProvider(), [
    'profiler.cache_dir' => __DIR__.'/../../var/cache/profiler',
]);
