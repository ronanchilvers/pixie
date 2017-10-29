<?php

namespace Pixie;

use Pixie\Application;

abstract class Controller
{
    /**
     * The application object
     *
     * @var Pixie\Application
     */
    private $app;

    /**
     * Class constructor
     *
     * @param  Pixie\Application $app
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Get the application object
     *
     * @return Pixie\Application
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    protected function app()
    {
        return $this->app;
    }

    /**
     * Get the session object
     *
     * @return Symfony\Component\HttpFoundation\Session\SessionInterface
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    protected function session()
    {
        return $this->app()['session'];
    }

    /**
     * Render a template
     *
     * @param  string $view
     * @param  array $params
     * @param  Symfony\Component\HttpFoundation\Response
     * @return Symfony\Component\HttpFoundation\Response
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function render($view, array $parameters = array(), Response $response = null)
    {
        if ('.html.twig' != substr($view, -10)) {
            $view .= '.html.twig';
        }

        return $this->app()->render($view, $parameters, $response);
    }

    /**
     * Redirect to a url
     *
     * @param  string $url
     * @param  int $status
     * @return Symfony\Component\HttpFoundation\RedirectResponse
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    protected function redirect($url, $status = 302)
    {
        return $this->app()->redirect($url, $status);
    }
}
