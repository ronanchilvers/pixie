<?php

namespace App;

use Silex\Application\TwigTrait;
use Silex\Application as SilexApplication;

class Application extends SilexApplication
{
    use TwigTrait;

    /**
     * Get the base path of the application
     *
     * @return string
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function getBasePath()
    {
        return realpath(__DIR__ . '/../..');
    }
}
