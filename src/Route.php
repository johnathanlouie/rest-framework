<?php

namespace Lwd\RestFramework;

use Lwd\RestFramework\Exceptions\BadRoute;

/**
 * Route is a mapping from path to controller.
 * 
 * @internal
 */
class Route
{
    /** @var string Literal path or with parameters. */
    private $path;

    /** @var string[] Path elements. */
    private $elements;

    /** @var Controller Controller. */
    private $controller;

    /**
     * Constructs the path for the route.
     * 
     * @param string $path Route path.
     * @param Controller $controller Controller.
     */
    public function __construct($path, $controller)
    {
        $this->$path = $path;
        $this->$controller = $controller;
        $this->elements = explode('/', $path);
    }

    /**
     * Checks if the path contains a variable or if a path element is variable.
     * 
     * @param string $path Whole path or path element.
     * @return bool
     */
    private static function containsParam($path)
    {
        return strpos($path, '{') !== false;
    }

    /**
     * Validates and returns the parameter name.
     * 
     * @param string $element Path element.
     * @return string Parameter name.
     * @throws BadRoute If route has invalid parameter syntax.
     */
    private function validateParam($element)
    {
        if (preg_match('^\\{([A-Za-z0-9_]+)\\}$', $element, $matches)) {
            return $matches[1];
        }
        throw new BadRoute($this->path);
    }

    /**
     * Checks if this route matches the request path.
     * 
     * @param RequestPath $requestPath The request path.
     * @return bool
     */
    public function matches($requestPath)
    {
        if ($this->path === $requestPath->getPath()) {
            return true;
        } else if (!self::containsParam($this->path)) {
            return false;
        } else if (count($this->elements) === $requestPath->getCount()) {
            foreach (array_map(null, $this->elements, $requestPath->getElements()) as $elements) {
                $route = $elements[0];
                $request = $elements[1];
                if (!self::containsParam($route) && $route !== $request) {
                    return false;
                }
            }
            return true;
        }
        return false;
    }

    /**
     * Returns the route parameters and arguments.
     * 
     * @param RequestPath $requestPath Request path.
     * @return array Parameter/argument map.
     * @throws BadRoute If route has invalid parameter syntax.
     */
    public function params($requestPath)
    {
        if (!self::containsParam($this->path)) {
            return [];
        }
        $params = [];
        $count = count($this->elements);
        $requestElements = $requestPath->getElements();
        for ($i = 0; $i < $count; $i++) {
            $route = $this->elements[$i];
            if (self::containsParam($route)) {
                $params[$this->validateParam($route)] = $requestElements[$i];
            }
        }
        return $params;
    }

    /**
     * Gets the controller for this route.
     * 
     * @return Controller
     */
    public function getController()
    {
        return $this->controller;
    }

    public function __toString()
    {
        return $this->path;
    }
}
