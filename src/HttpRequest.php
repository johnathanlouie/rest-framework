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
     * Gets which HTTP request method was used to access the page; e.g. 'GET', 'HEAD', 'POST', 'PUT'.
     * 
     * @return string HTTP request method.
     */
    public function getHttpMethod()
    {
        return $_SERVER['REQUEST_METHOD'];
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
