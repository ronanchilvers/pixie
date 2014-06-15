<?php

namespace Pixie\Web;

interface CommandInterface
{
    /**
     * Get the path for this command
     *
     * This is the path that the web application should respond
     * on.
     *
     * @return string
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function getPath();

    /**
     * Get the closure that this command should execute
     *
     * @return Closure
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function getClosure();
}
