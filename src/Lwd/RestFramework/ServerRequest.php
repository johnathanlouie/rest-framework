<?php

namespace Lwd\RestFramework;

use Lwd\Http\Message\ServerRequestInterface;
use Lwd\Http\Message\StreamInterface;
use Lwd\Http\Message\UriInterface;

class ServerRequest extends Request implements ServerRequestInterface
{
    /** @var array Request cookies */
    private $cookies;

    /** @var array Server parameters */
    private $serverParams;

    /** @var array URL query parameters */
    private $queryParams;

    /** @var array Uploaded files information */
    private $uploadedFiles;

    /** @var array|object|null Parsed request body */
    private $parsedBody;

    /** @var array Request attributes */
    private $attributes;

    /**
     * Constructs a new server request.
     *
     * @param string $method The HTTP method (GET, POST, PUT, etc.)
     * @param UriInterface $uri The URI object representing the request URI
     * @param string $protocolVersion HTTP protocol version (e.g., "1.0", "1.1", "2.0")
     * @param array $headers Request headers, normalized according to PSR-7 requirements
     * @param StreamInterface|null $body Request body as a stream object
     * @param string|null $requestTarget Request target (if different from URI path), in the form of an absolute path, authority form, or asterisk form
     * @param array $serverParams The server parameters, typically derived from $_SERVER
     * @param array $cookies The request cookies, typically derived from $_COOKIE
     * @param array $queryParams The URL query string parameters, typically derived from $_GET
     * @param array|object|null $parsedBody The parsed request body, typically derived from $_POST or parsed from raw request body (JSON, XML, etc.)
     * @param array $uploadedFiles The normalized file upload data, typically derived from $_FILES
     * @param array $attributes The request attributes, arbitrary values associated with the request that can be used by middleware and handlers
     */
    public function __construct(
        $method,
        $uri,
        $protocolVersion,
        $headers,
        $body,
        $requestTarget,
        $serverParams,
        $cookies,
        $queryParams,
        $parsedBody,
        $uploadedFiles,
        $attributes
    ) {
        parent::__construct($method, $uri, $protocolVersion, $headers, $body, $requestTarget);
        $this->serverParams = $serverParams;
        $this->cookies = $cookies;
        $this->queryParams = $queryParams;
        $this->parsedBody = $parsedBody;
        $this->uploadedFiles = $uploadedFiles;
        $this->attributes = $attributes;
    }

    /**
     * @inheritDoc
     */
    public function getServerParams()
    {
        return $this->serverParams;
    }

    /**
     * @inheritDoc
     */
    public function getCookieParams()
    {
        return $this->cookies;
    }

    /**
     * @inheritDoc
     */
    public function withCookieParams($cookies)
    {
        $new = clone $this;
        $new->cookies = array_merge($this->cookies, $cookies);
        return $new;
    }

    /**
     * @inheritDoc
     */
    public function getQueryParams()
    {
        return $this->queryParams;
    }

    /**
     * @inheritDoc
     */
    public function withQueryParams($query)
    {
        $new = clone $this;
        $new->queryParams = $query;
        return $new;
    }

    /**
     * @inheritDoc
     */
    public function getUploadedFiles()
    {
        return $this->uploadedFiles;
    }

    /**
     * @inheritDoc
     */
    public function withUploadedFiles($uploadedFiles)
    {
        $new = clone $this;
        $new->uploadedFiles = $uploadedFiles;
        return $new;
    }

    /**
     * @inheritDoc
     */
    public function getParsedBody()
    {
        return $this->parsedBody;
    }

    /**
     * @inheritDoc
     */
    public function withParsedBody($data)
    {
        $new = clone $this;
        $new->parsedBody = $data;
        return $new;
    }

    /**
     * @inheritDoc
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @inheritDoc
     */
    public function getAttribute($name, $default = null)
    {
        return array_key_exists($name, $this->attributes)
            ? $this->attributes[$name]
            : $default;
    }

    /**
     * @inheritDoc
     */
    public function withAttribute($name, $value)
    {
        $new = clone $this;
        $new->attributes[$name] = $value;
        return $new;
    }

    /**
     * @inheritDoc
     */
    public function withoutAttribute($name)
    {
        $new = clone $this;
        unset($new->attributes[$name]);
        return $new;
    }
}
