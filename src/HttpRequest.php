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
     * Sets route parameters and arguments.
     * 
     * @param string[] $routeParams Parameters and arguments map.
     * @return void
     */
    public function setRouteParams($routeParams)
    {
        $this->routeParams = $routeParams;
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
