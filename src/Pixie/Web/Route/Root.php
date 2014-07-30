<?php

namespace Pixie\Web\Route;

class Root extends Base
{
    protected $path = '/';

    public function execute(\Pixie\Web\Route\Context $context)
    {
        $context->app->redirect('/listing');
    }
}
