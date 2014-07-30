<?php

namespace Pixie\Web\Route\Deployment;

use Pixie\Environment;
use Pixie\Web\Route\Base;
use Pixie\Item\App;

class Listing extends Base
{
    protected $path = '/listing';
    protected $template = 'deployment/listing.phtml';

    public function execute(\Pixie\Web\Route\Context $context)
    {
        $this->view()->listingFields     = App::getListingFields();
        $this->view()->apps              = App::find();
    }
}
