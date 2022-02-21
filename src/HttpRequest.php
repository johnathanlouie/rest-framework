<?php

namespace Lwd\RestFramework;

use OutOfRangeException;

/**
 * HTTP request data.
 */
class HttpRequest
{
    /** @var array Route parameters and arguments. */
    private $routeParams = [];

    /**
     * Constructs the HTTP request.
     */
    public function __construct()
    {
        // TODO: Add other request data.
    }

    /**
     * Sets a route parameter and argument.
     * 
     * @param string $name Parameter name.
     * @param string $value Argument value.
     * @return void
     */
    public function setRouteParam($name, $value)
    {
        $this->routeParams[$name] = $value;
    }

    /**
     * Gets the value of the route parameter.
     * 
     * @param string $param Route parameter.
     * @return string Route argument.
     * @throws OutOfRangeException If the parameter does not exist.
     */
    public function getRouteArg($param)
    {
        if (!array_key_exists($param, $this->routeParams)) {
            throw new OutOfRangeException();
        }
        return $this->routeParams[$param];
    }
}
