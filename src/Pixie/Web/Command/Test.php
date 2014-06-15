<?php

namespace Pixie\Web\Command;

class Test extends Base
{
    public function getPath()
    {
        return '/test';
    }

    public function getClosure()
    {
        return function() {
            echo 'hallo';
            exit;
        };
    }
}
