<?php

namespace Lwd\RestFramework;

/**
 * A route is a many-to-one mapping of URLs (HTTP methods and paths) to a controller.
 * 
 * @internal
 */
final class Route
{
    /** @var string[] Paths supported by the controller. */
    private $paths;

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
        $this->paths = $paths;
        $this->controller = $controller;
    }

    /**
     * Checks if the route contains a variable or if a path component is variable.
     * 
     * @param string $path Whole request path or component.
     * @return bool
     */
    public static function containsParam($path)
    {
        return strpos($path, '{') !== false;
    }

    /**
     * Checks if a route path matches the request path.
     * 
     * @param string $route A route path to match.
     * @param string $request The request path.
     * @return bool
     */
    private static function isMatch($route, $request)
    {
        $route = explode('/', $route);
        $request = explode('/', $request);
        if (count($route) === count($request)) {
            foreach (array_map(null, $route, $request) as $e) {
                if (!self::containsParam($e[0]) && $e[0] !== $e[1]) {
                    return false;
                }
            }
            return true;
        }
        return false;
    }

    /**
     * Returns a matching route path.
     * 
     * @return string|null Route path, or null if no match.
     */
    public function findMatch($path)
    {
        foreach ($this->paths as $route) {
            if ($route === $path || self::containsParam($route) && self::isMatch($route, $path)) {
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
