<?php

namespace Pixie\Web\Route\Deployment;

use Pixie\Environment;
use Pixie\Web\Route\Base;
use Pixie\Item\App;

class Delete extends Base
{
    protected $verbs        = array('GET');
    protected $path         = '/delete/:id';

    public function execute(\Pixie\Web\Route\Context $context)
    {
        $args   = $context->args;
        $id     = (int) array_shift($args);
        $delApp = App::find((int) $id);
        if ($delApp instanceof App) {
            if (!$delApp->destroy()) {
                // @todo Flash message
            }
        }

        $context->app->redirect('/listing');
    }
}
