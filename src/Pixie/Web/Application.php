<?php

namespace Pixie\Web;

use Pixie\Environment;
use Pixie\Web\Route;
use Pixie\Web\View;
use Slim\Slim;

class Application extends Slim
{
    /**
     * Class constructor
     *
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function __construct(array $userSettings = array())
    {
        $env = Environment::instance();
        $userSettings['debug'] = true;
        $userSettings['templates.path'] = $env->getTemplateDir();
        $userSettings['view'] = new View();

        parent::__construct($userSettings);
    }

    /**
     * Run this application
     *
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function run()
    {
        $this->setupRoutes();
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
        $commands = $this->getDefaultRoutes();
        foreach ($commands as $command) {
            $verb = $command->getVerb();
            $this->{$verb}($command->getPath(), $command->getClosure());
        }
    }

    /**
     * Get an array of default routes for this application
     *
     * @return array
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    protected function getDefaultRoutes()
    {
        return array(
                new Route\Root($this),
                new Route\Listing($this)
            );
    }
}
