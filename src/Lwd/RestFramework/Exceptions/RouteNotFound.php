<?php

namespace Lwd\RestFramework\Exceptions;

use RuntimeException;

/**
 * Request path has no matching route.
 */
class RouteNotFound extends RuntimeException
{
    /**
     * Constructs the exception.
     * 
     * @param string $requestPath
     */
    public function __construct($requestPath)
    {
        parent::__construct("No matching route: path '$requestPath'");
    }
}
