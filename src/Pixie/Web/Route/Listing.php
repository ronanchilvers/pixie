<?php

namespace Pixie\Web\Route;

use Pixie\Environment;

class Listing extends Base
{
    public function getPath()
    {
        return '/listing';
    }

    public function getClosure()
    {
        $app = $this->app();
        return function() use ($app) {
            $app->view()->name = 'ronan';
            $app->view()->config = Environment::instance()->getConfig();
            $app->render('listing.phtml');
        };
    }
}
