<?php

namespace Pixie;

use Pixie\Context\Singleton;

class Config extends Singleton
{
    protected static function setup($instance)
    {
        $instance
            ->once('root', function() {
                return __DIR__ . '/../../';
            })
            ;
    }
}
