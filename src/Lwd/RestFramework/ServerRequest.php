<?php

namespace Lwd\RestFramework;

use Lwd\Http\Message\ServerRequestInterface;
use Lwd\Http\Message\StreamInterface;
use Lwd\Http\Message\UriInterface;

class ServerRequest implements ServerRequestInterface
{
    /** @var string HTTP protocol version */
    private $protocolVersion;

    /** @var array HTTP headers */
    private $headers;

    /** @var StreamInterface|null Request body */
    private $body;

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

    /** @var string|null Custom request target */
    private $requestTarget;

    /** @var string HTTP request method */
    private $method;

    /** @var UriInterface Request URI */
    private $uri;

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
        $this->method = $method;
        $this->uri = $uri;
        $this->protocolVersion = $protocolVersion;
        $this->headers = $headers;
        $this->body = $body;
        $this->requestTarget = $requestTarget;
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

    /**
     * @inheritDoc
     */
    public function getProtocolVersion()
    {
        return $this->protocolVersion;
    }

    /**
     * @inheritDoc
     */
    public function withProtocolVersion($version)
    {
        $new = clone $this;
        $new->protocolVersion = $version;
        return $new;
    }

    /**
     * @inheritDoc
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @inheritDoc
     */
    public function hasHeader($name)
    {
        foreach ($this->headers as $header => $values) {
            if (strtolower($header) === strtolower($name)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @inheritDoc
     */
    public function getHeader($name)
    {
        foreach ($this->headers as $header => $values) {
            if (strtolower($header) === strtolower($name)) {
                return $values;
            }
        }
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getHeaderLine($name)
    {
        $values = $this->getHeader($name);
        if (empty($values)) {
            return '';
        }
        return implode(', ', $values);
    }

    /**
     * @inheritDoc
     */
    public function withHeader($name, $value)
    {
        $new = clone $this;
        $value = is_array($value) ? $value : [$value];

        $headerFound = false;
        foreach ($new->headers as $header => $values) {
            if (strtolower($header) === strtolower($name)) {
                $new->headers[$header] = $value;
                $headerFound = true;
                break;
            }
        }

        if (!$headerFound) {
            $new->headers[$name] = $value;
        }

        return $new;
    }

    /**
     * @inheritDoc
     */
    public function withAddedHeader($name, $value)
    {
        if (!$this->hasHeader($name)) {
            return $this->withHeader($name, $value);
        }

        $new = clone $this;
        $value = is_array($value) ? $value : [$value];

        foreach ($new->headers as $header => $values) {
            if (strtolower($header) === strtolower($name)) {
                $new->headers[$header] = array_merge($values, $value);
                break;
            }
        }

        return $new;
    }

    /**
     * @inheritDoc
     */
    public function withoutHeader($name)
    {
        $new = clone $this;

        foreach ($new->headers as $header => $values) {
            if (strtolower($header) === strtolower($name)) {
                unset($new->headers[$header]);
                break;
            }
        }

        return $new;
    }

    /**
     * @inheritDoc
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @inheritDoc
     */
    public function withBody($body)
    {
        $new = clone $this;
        $new->body = $body;
        return $new;
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
