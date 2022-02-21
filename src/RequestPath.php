<?php

namespace Lwd\RestFramework;

/**
 * Request path.
 * 
 * @internal
 */
class RequestPath
{
    /** @var string Request path. */
    private $path;

    /** @var string Base path. */
    private $basePath;

    /** @var string Path split by slashes. */
    private $elements;

    /** @var int Number of elements. */
    private $count;

    /**
     * Constructs the request path.
     * 
     * @param string $basePath Base path.
     */
    public function __construct($basePath)
    {
        $this->$basePath = $basePath;
        $this->path = substr($_SERVER['REQUEST_URI'], strlen($basePath));
        $this->elements = explode('/', $this->path);
        $this->count = count($this->elements);
    }

    /**
     * Gets the request path.
     * 
     * @return string Request path.
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Gets the base path.
     * 
     * @return string Base path.
     */
    public function getBasePath()
    {
        return $this->basePath;
    }

    /**
     * Gets the number of elements in the path.
     * 
     * @return int Number of elements.
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * Gets the path elements.
     * 
     * @return string[] Path elements.
     */
    public function getElements()
    {
        return $this->elements;
    }

    public function __toString()
    {
        return $this->path;
    }
}
