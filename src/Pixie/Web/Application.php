<?php

namespace Pixie\Web;

use Pixie\Environment;
use Pixie\Web\Route;
use Pixie\Web\View;
use Slim\Slim;

class Application extends Slim
{
    /**
     * The page view
     *
     * @var Pixie\Web\View
     */
    protected $page;

    /**
     * Class constructor
     *
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function __construct(array $userSettings = array())
    {
        $env                                = Environment::instance();
        $userSettings['debug']              = true;
        $userSettings['templates.path']     = $env->getTemplateDir();
        $userSettings['view']               = new View();

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
            $route = $this->mapRoute(array(
                    $command->getPath(),
                    $command->getClosure()
                )
            );
            foreach ($command->getVerbs() as $verb) {
                $route->via($verb);
            }
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
                new Route\Deployment\Listing($this),
                new Route\Deployment\Add($this),
                new Route\Deployment\Delete($this),
                new Route\Root($this),
            );
    }
}
