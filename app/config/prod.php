<?php

use App\Controller\ErrorController;
use App\Controller\TestController;
use Symfony\Component\HttpFoundation\Request;

// ****************************************************************
// Error controller
$app['controller.error'] = function () use ($app) {
    return new ErrorController($app);
};
$app->error(function (\Exception $ex, Request $request, $code) use ($app) {
    return $app['controller.error']->errorAction($ex, $request, $code);
});
