<?php

namespace Lwd\RestFramework;

/**
 * HTTP request data.
 */
class HttpRequest
{
    /** @var array Arguments from route parameters. */
    private $pathArgs = [];

    /**
     * @param array $pathArgs Arguments from route parameters.
     */
    public function __construct($pathArgs = [])
    {
        $this->pathArgs = $pathArgs;
    }

    /**
     * Gets the value of the route parameter.
     * 
     * @param string $param Route parameter.
     * @return null|string|int|float|bool Argument.
     */
    public function pathArg($param)
    {
        return $this->pathArgs[$param];
    }
}
