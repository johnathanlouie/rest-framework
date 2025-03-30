<?php

namespace Lwd\RestFramework\Exceptions;

use Lwd\RestFramework\BaseException;

/**
 * Route has invalid syntax.
 */
class BadRoute extends BaseException
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
