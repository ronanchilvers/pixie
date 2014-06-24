<?php

namespace Pixie\Web\Route;

class Test extends Base
{
    protected $path = '/test';

    public function getClosure()
    {
        return function() {
            echo 'hallo';
        };
    }
}
