<?php

namespace Pixie\Web\Route;

use Pixie\Web\RouteInterface;
use Pixie\Web\Route\Context;

abstract class Base implements RouteInterface
{
    /**
     * The HTTP verb that this route should respond to
     *
     * @var string
     */
    protected $verbs = array('GET');

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
     * The layouts to use for this route
     *
     * @var array
     */
    protected $layouts = array(
            'layouts/page.phtml'
        );

    /**
     * The template to render for this action
     *
     * @var string
     */
    protected $template;

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
    public function getVerbs()
    {
        $verbs = $this->verbs;
        if (!is_array($verbs)) {
            $verbs = array($verbs);
        }
        $verbs = array_map(function ($value) {
            return strtoupper($value);
        }, $this->verbs);

        return $verbs;
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
     * Get the view object for this Route
     *
     * @return Pixie\Web\View
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    protected function view()
    {
        return $this->app()->view();
    }

    /**
     * Get the closure object for this route
     *
     * @return Closure
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function getClosure()
    {
        $app = $this->app;

        return function () use ($app) {
            $args = func_get_args();
            $context = Context::Factory()
                        ->service('app', $app)
                        ->service('args', $args)
                        ;
            $this->execute($context);
            if (is_array($this->layouts) && 0 < count($this->layouts)) {
                foreach ($this->layouts as $layout) {
                    $this->view()->addLayout($layout);
                }
            }
            $app->render($this->template);
        };
    }

    /**
     * Get the closure that should execute when this command fires
     *
     * @return Closure
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    abstract public function execute(\Pixie\Web\Route\Context $context);
}
