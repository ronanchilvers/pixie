<?php

namespace Pixie;

use \Interop\Container\ContainerInterface;
use \Pixie\Context\ContainerException;
use \Pixie\Context\NotFoundException;

class Context implements ContainerInterface
{
    /**
     * Standard factory method
     *
     * This method is really only here to support the fluid
     * interface so you can do
     *
     * <code>
     * $ctx = Context::Factory()->service('one', $one)->service('two', $two);
     * </code>
     *
     * which is nice.
     *
     * @return static
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public static function Factory()
    {
        return new static();
    }

    /**
     * Registry array for services for this context
     *
     * The internal format of the array is
     *
     * <code>
     *  array(
     *      'name' => array(
     *              'service' => <var or callable>,
     *              'singleton' => <boolean>
     *          )
     *  )
     * </code>
     *
     * @var array
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    protected $_services = array();

    /**
     * Call method overriden to provide magic get*()
     *
     * @param string $method
     * @param array $args
     * @return mixed
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function __call($method, $args)
    {
        $method = strtolower($method);
        $prefix = substr($method, 0, 3);
        if ('get' == $prefix) {
            $name = substr($method, 3);
            return $this->get($name);
        }
    }

    /**
     * Set a service definition into the services registry
     *
     * A service can be a resolved variable or a callable. If
     * its a callable, it will be executed every time the service
     * is requested.
     *
     * @param string $name
     * @param mixed $service
     * @return $this
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function service($name, $service)
    {
        return $this->register($name, $service, false);
    }

    /**
     * Add a singleton service
     *
     * A singleton service must be a callback and is fired only
     * once (the first time the service is requested) during the
     * lifetime of the context. The return value is stored and
     * thereafter that return value is returned.
     *
     * It is expected that the service passed in is always a
     * callable, otherwise there's no benefit.
     *
     * @param string $name
     * @param callable $service
     * @return $this
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function once($name, $service)
    {
        return $this->register($name, $service, true);
    }

    /**********************************/
    /** Container-Interop compliance **/

    /**
     * Has this context got a service for a given key
     *
     * @param string $id The identifier for the service to check
     * @return boolean
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function has($id)
    {
        $id = strtolower($id);
        return isset($this->_services[$id]);
    }

    /**
     * Get a service by key
     *
     * @param string $id The identifier for the service to get
     * @return mixed
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function get($id)
    {
        $id = strtolower($id);
        if (!$this->has($id)) {
            throw new NotFoundException('Key ' . $id . ' not found');
        }
        if (isset($this->_services[$id])) {
            $service = &$this->_services[$id];
            if (is_callable($service['service'])) {
                try {
                    $value = $service['service']();
                    if (true === $service['singleton']) {
                        $service['service'] = $value;
                    };
                    return $value;
                }
                catch (\Exception $ex) {
                    throw new ContainerException($ex->getMessage());
                }
            }
            else {
                return $service['service'];
            }
        }
        return null;
    }

    /** Container-Interop compliance **/
    /**********************************/

    /**
     * Internal method for adding services
     *
     * A service can be a fixed variable or a callable. If the singleton
     * param is true and the service is a callable then the callable is
     * only executed once and thereafter the same resolved value is returned.
     *
     * @param string $name
     * @param mixed $service
     * @param boolean $singleton
     * @return $this
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    protected function register($name, $service, $singleton)
    {
        $name = strtolower($name);
        $this->_services[$name] = array(
                'service'       => $service,
                'singleton'     => $singleton
            );
        return $this;
    }
}
