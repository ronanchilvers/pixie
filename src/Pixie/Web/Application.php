<?php

namespace Pixie\Web;

use Slim\Slim;
use Pixie\Web\Command;

class Application extends Slim
{
    /**
     * Class constructor
     *
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    // public function __construct(array $userSettings = array())
    // {
    //     parent::__construct();
    // }

    /**
     * Run this application
     *
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function run()
    {
        // $this->setupRoutes();
        return parent::run();
    }

    /**
     * Setup routes for this application
     *
     * @return void
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    protected function setupRoutes()
    {
        $commands = $this->getDefaultCommands();
        foreach ($commands as $command) {
            $verb = $command->getVerb();
            $this->{$verb}($command->getPath(), $command->getClosure());
        }
    }

    /**
     * Get an array of default commands / routes for this application
     *
     * @return array
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    protected function getDefaultCommands()
    {
        return array(
                new Command\Test()
            );
    }
}
