<?php

namespace Lwd\RestFramework\Exceptions;

use RuntimeException;

/**
 * Route path has invalid syntax.
 */
class BadRoutePath extends RuntimeException
{
    /**
     * Constructs the exception.
     * 
     * @param string $path
     */
    public function __construct($path)
    {
        parent::__construct("Invalid route path syntax: $path");
    }
}
