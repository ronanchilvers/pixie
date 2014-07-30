<?php

namespace Pixie\Web;

use Slim\View as SlimView;

class View extends SlimView
{
    /**
     * The layouts to use for this view
     *
     * @var array
     */
    protected $layouts = array();

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
     * Add a layout
     *
     * @param string $layout
     * @return $this
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function addLayout($layout)
    {
        $this->layouts[$layout] = $layout;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function render($template, $data = null)
    {
        $content = $this->renderTemplate($template, $data);

        if (is_array($this->layouts) && 0 < count($this->layouts)) {
            foreach ($this->layouts as $layout) {
                try {
                    $content        = $this->renderTemplate($layout, array('content' => $content));
                }
                catch (\RuntimeException $ex) {
                    // log the error somewhere
                    var_dump($ex->getMessage());
                }
            }
        }

        return $content;
    }

    /**
     * Render a template and return the rendered string
     *
     * @param string $template
     * @return string
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    protected function renderTemplate($template, $data = null)
    {
        $templatePathname = $this->getTemplatePathname($template);
        if (!is_file($templatePathname)) {
            throw new \RuntimeException("View cannot render `$template` because the template does not exist");
        }

        $data = array_merge($this->data->all(), (array) $data);
        $this->replace($data);
        extract($data); // kept for Slim compatibility
        ob_start();
        require $templatePathname;

        return ob_get_clean();
    }
}
