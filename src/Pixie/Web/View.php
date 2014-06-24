<?php

namespace Pixie\Web;

use Slim\View as SlimView;

class View extends SlimView
{
    protected $_params = array();

    /**
     * Magic get for view variables
     *
     * @param string $key
     * @return $this
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function __get($key)
    {
        return $this->get($key);
    }

    /**
     * Magic set for view variables
     *
     * @param string $key
     * @param mixed $value
     * @return $this
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function __set($key, $value)
    {
        return $this->set($key, $value);
    }

    /**
     * Override Slim set() to provide fluent interface
     *
     * @param string $key
     * @param mixed $value
     * @return $this
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function set($key, $value)
    {
        parent::set($key, $value);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function render($template, $data = null)
    {
        return parent::render($template, $data);
    }
}
