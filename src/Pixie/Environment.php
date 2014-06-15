<?php

namespace Pixie;

use Pixie\Context\Singleton;

class Environment extends Singleton
{
    protected static function setup($instance)
    {
        $instance
            ->service('version', '%PIXIE_VERSION%')
            ;
    }
}
