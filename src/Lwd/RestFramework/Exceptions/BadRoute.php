<?php

namespace Lwd\RestFramework\Exceptions;

use RuntimeException;

/**
 * Route has invalid syntax.
 */
class BadRoute extends RuntimeException
{
    /**
     * Constructs the exception.
     * 
     * @param string $route
     */
    public function __construct($route)
    {
        parent::__construct("Invalid route syntax: $route");
    }
}
