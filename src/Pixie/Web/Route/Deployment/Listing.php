<?php

namespace Pixie\Web\Route\Deployment;

use Pixie\Environment;
use Pixie\Web\Route\Base;
use Pixie\Item\App;

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
            $app->view()->listingFields     = App::getListingFields();
            $app->view()->apps              = App::find();
            $app->render('listing.phtml');
        };
    }
}
