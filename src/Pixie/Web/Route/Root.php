<?php

namespace Pixie\Web\Route;

class Root extends Base
{
    public function getPath()
    {
        return '/';
    }

    public function getClosure()
    {
        $app = $this->app();
        return function() use ($app) {
            $app->redirect('/listing');
        };
    }
}
