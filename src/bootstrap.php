<?php

function includeIfExists($file)
{
    return file_exists($file) ? include $file : false;
}

if (!$loader = includeIfExists(__DIR__.'/../vendor/autoload.php')) {
    echo 'You must set up the project dependencies, run the following commands:'.PHP_EOL.
        'curl -sS https://getcomposer.org/installer | php'.PHP_EOL.
        'php composer.phar install'.PHP_EOL;
    exit(1);
}

if (function_exists('ini_set')) {
    ini_set('error_log', __DIR__ . '/error.log');
    ini_set('display_errors', true);
    // @TODO Remove var_dump
    if ('cli' !== php_sapi_name()) {
        ini_set('html_errors', true);
    }
}

// Application wide definitions
$root = __DIR__ . '/';
if (basename(__FILE__) == 'bootstrap.php') {
    $root .= '../';
}
define('PIXIE_ROOT', $root);

return $loader;
