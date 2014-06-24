<?php

namespace Pixie\Web\Route;

use Pixie\Web\RouteInterface;

abstract class Base implements RouteInterface
{
    /**
     * The HTTP verb that this route should respond to
     *
     * @var string
     */
    protected $verb = 'GET';

    /**
     * The path for this command
     *
     * @var string
     */
    protected $path;

    /**
     * The slim application that this command is attached to
     *
     * @var Pixie\Web\Application
     */
    protected $app = false;

    /**
     * Class constructor
     *
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function __construct(\Pixie\Web\Application $app)
    {
        $this->app = $app;
    }

    /**
     * Get the HTTP verb that this view should respond to
     *
     * @return string
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function getVerb()
    {
        return strtolower($this->verb);
    }

    /**
     * Get the URL path that this command should respond on
     *
     * @return string
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Get the app instance for this command
     *
     * @return Pixie\Web\Application
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    protected function app()
    {
        return $this->app;
    }

    /**
     * Get the closure that should execute when this command fires
     *
     * @return Closure
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    abstract public function getClosure();
}
