<?php

namespace Pixie\Console;

use Pixie\Console;
use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Command\HelpCommand;

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

        // Configuration
        $defaultCommands[]  = new Command\Config\Db();

        // Deployments
        $defaultCommands[]  = new Command\Deployment\Listing();
        $defaultCommands[]  = new Command\Deployment\Add();
        $defaultCommands[]  = new Command\Deployment\Delete();

        // Queue
        $defaultCommands[]  = new Command\Queue\Listing();
        return $defaultCommands;
    }
}
