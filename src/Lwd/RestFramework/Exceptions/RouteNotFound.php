<?php

namespace Lwd\RestFramework\Exceptions;

use Lwd\RestFramework\BaseException;

/**
 * Request path has no matching route.
 */
class RouteNotFound extends BaseException
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
