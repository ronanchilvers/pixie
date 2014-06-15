<?php

namespace Pixie\Console;

use Pixie\Console;
use Symfony\Component\Console\Application as BaseApplication;

class Application extends BaseApplication
{
    /**
     * Override to get a set of default commands
     *
     * @return Command[]
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function getDefaultCommands()
    {
        $defaultCommands    = parent::getDefaultCommands();
        $defaultCommands[]  = new Command\TestCommand();
        return $defaultCommands;
    }
}
