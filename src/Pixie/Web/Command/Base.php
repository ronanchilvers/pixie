<?php

namespace Pixie\Web\Command;

use Pixie\Web\CommandInterface;

abstract class Base implements CommandInterface
{
    /**
     * The HTTP verb that this route should respond to
     *
     * @var string
     */
    protected $verb = 'GET';

    public function getVerb()
    {
        return strtolower($this->verb);
    }

    abstract public function getPath();
    abstract public function getClosure();
}
