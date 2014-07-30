<?php

namespace TLD\View;

class Standard extends \TLD\View
{
    /**
     * The directory where layout templates are stored
     *
     * @var string
     * @access protected
     */
    protected $_layoutDirectory = false;

    /**
     * The suffix (usually filename extension)
     *
     * @var string
     * @access protected
     */
    protected $_renderTemplateSuffix = 'phtml';

    /**
     * Layouts that this view should be wrapped
     * with. Layouts are rendered in the order
     * that they occur in the array.
     *
     * @var array
     * @access protected
     */
    protected $_layouts = array();


    /**
     * Render a template file, wrapping it in any layouts as appropriate
     *
     * This method is overriden from the Slim\View superclass
     *
     * @param  string $template     The template pathname, relative to the template base directory
     * @param  array  $data         Any additonal data to be passed to the template.
     * @return string               The rendered template
     * @throws \RuntimeException    If resolved template pathname is not a valid file
     */
    protected function render($template, $data = null)
    {
        $content = '';

        // Render the template file first
        $templatePathname = $this->getTemplatePathname($template);
        if (!is_file($templatePathname)) {
            throw new \RuntimeException("View cannot render `$template` because the template does not exist");
        }

        $content = $this->renderFilename($templatePathname, $data);

        if (0 < count($this->_layouts))
        {
            if (false == $this->_layoutDirectory)
            {
                throw new \RuntimeException("View cannot render layouts as there is no layout template directory specified");
            }

            foreach ($this->_layouts as $layoutName)
            {
                $layoutPathname = $this->getLayoutPathname($layoutName);
                $data['content'] = $content;

                $content = $this->renderFilename($layoutPathname, $data);
            }
        }

        return $content;
    }

    /**
     * Render a template file and return it
     *
     * This method actually renders a file. It is used internally
     * by render() above.
     *
     * @var string $filename
     * @data array
     * @access protected
     */
    protected function renderFilename($filename, $data)
    {
        $data = array_merge($this->data->all(), (array) $data);
        extract($data);
        ob_start();
        require $filename;

        return ob_get_clean() . "\n";
    }

    /**
     * Get the full file path for a layout
     *
     * @var string $layoutName
     * @return string
     * @access protected
     */
    protected function getLayoutPathname($layoutName)
    {
        return $this->_layoutDirectory . '/' . $layoutName . '.' . $this->_renderTemplateSuffix;
    }

    /**
     * Set the layouts that this view should use
     *
     * @var array $layouts
     * @access public
     * @return $this
     */
    public function setLayouts(array $layouts)
    {
        $this->_layouts = $layouts;
        return $this;
    }

    /**
     * Set the template directory for layouts
     *
     * @var string $directory
     * @access public
     * @return $this
     */
    public function setLayoutDirectory($directory)
    {
        $this->_layoutDirectory = $directory;
        return $this;
    }

}