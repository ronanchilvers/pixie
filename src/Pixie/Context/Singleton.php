<?php

namespace Pixie\Context;

use Pixie\Context;

class Singleton extends Context
{
    /**
     * The singleton instance of this class
     *
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    static protected $instance = null;

    /**
     * Standard singleton static getter
     *
     * @return
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    static public function instance()
    {
        if (false == (static::$instance instanceof static))
        {
            static::$instance = static::Factory();
            static::setup(static::$instance);
        }
        return static::$instance;
    }

    /**
     * Setup this singleton on instantiation
     *
     * @return void
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    protected static function setup(Singleton $instance)
    {}

    /**
     * Class constructor
     *
     * Protected to comply with the singleton pattern
     *
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    protected function __construct()
    {}

    /**
     * Magic clone method
     *
     * Protected to comply with the singleton pattern
     *
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    protected function __clone()
    {}

}
