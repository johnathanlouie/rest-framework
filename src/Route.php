<?php

namespace Lwd\RestFramework;

/**
 * A route is a many-to-one mapping of URLs (HTTP methods and paths) to a controller.
 * 
 * @internal
 */
final class Route
{
    /** @var RoutePath[] Paths supported by the controller. */
    private $paths = [];

    /** @var Controller Route controller. */
    private $controller;

    /**
     * Constructs the route.
     * 
     * @param string[] $paths Paths supported by the controller.
     * @param Controller $controller Route controller.
     */
    public function __construct($paths, $controller)
    {
        foreach ($paths as $path) {
            $this->paths[] = new RoutePath($path);
        }
        $this->controller = $controller;
    }

    /**
     * Returns a matching route path.
     * 
     * @param RequestPath $requestPath Request path.
     * @return RoutePath|null Route path, or null if no match.
     */
    public function findMatch($requestPath)
    {
        foreach ($this->paths as $route) {
            if ($route->matches($requestPath)) {
                return $route;
            }
        }
        return null;
    }

    /**
     * Gets the route controller for this route.
     * 
     * @return Controller
     */
    public function getController()
    {
        return $this->controller;
    }
}
