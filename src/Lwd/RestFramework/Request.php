<?php

namespace Lwd\RestFramework;

use Lwd\Http\Message\RequestInterface;
use Lwd\Http\Message\StreamInterface;
use Lwd\Http\Message\UriInterface;

/**
 * Implementation of an HTTP request.
 * 
 * This class represents an HTTP request message.
 */
class Request extends Message implements RequestInterface
{
    /** @var string|null Custom request target */
    protected $requestTarget;

    /** @var string HTTP request method */
    protected $method;

    /** @var UriInterface Request URI */
    protected $uri;

    /**
     * Constructs a new HTTP request.
     *
     * @param string $method The HTTP method (GET, POST, PUT, etc.)
     * @param UriInterface $uri The URI object representing the request URI
     * @param string $protocolVersion HTTP protocol version (e.g., "1.0", "1.1", "2.0")
     * @param array $headers Request headers, normalized according to PSR-7 requirements
     * @param StreamInterface|null $body Request body as a stream object
     * @param string|null $requestTarget Request target (if different from URI path)
     */
    public function __construct(
        $method,
        $uri,
        $protocolVersion,
        $headers,
        $body = null,
        $requestTarget = null
    ) {
        parent::__construct($protocolVersion, $headers, $body);
        $this->method = $method;
        $this->uri = $uri;
        $this->requestTarget = $requestTarget;
    }

    /**
     * @inheritDoc
     */
    public function getRequestTarget()
    {
        if ($this->requestTarget !== null) {
            return $this->requestTarget;
        }

        $target = '/';
        if ($this->uri !== null) {
            $path = $this->uri->getPath();
            if (!empty($path)) {
                $target = $path;
            }

            $query = $this->uri->getQuery();
            if (!empty($query)) {
                $target .= '?' . $query;
            }
        }

        return $target;
    }

    /**
     * @inheritDoc
     */
    public function withRequestTarget($requestTarget)
    {
        $new = clone $this;
        $new->requestTarget = $requestTarget;
        return $new;
    }

    /**
     * @inheritDoc
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @inheritDoc
     */
    public function withMethod($method)
    {
        $new = clone $this;
        $new->method = $method;
        return $new;
    }

    /**
     * @inheritDoc
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * @inheritDoc
     */
    public function withUri($uri, $preserveHost = false)
    {
        $new = clone $this;
        $new->uri = $uri;

        if ($preserveHost && $this->hasHeader('Host')) {
            return $new;
        }

        if ($uri->getHost() !== '') {
            $host = $uri->getHost();
            if ($uri->getPort() !== null) {
                $host .= ':' . $uri->getPort();
            }
            $new = $new->withHeader('Host', $host);
        }

        return $new;
    }
}
