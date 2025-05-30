<?php

namespace Lwd\RestFramework;

use Lwd\Http\Message\UriInterface;
use InvalidArgumentException;

/**
 * Implementation of a URI according to RFC 3986.
 * 
 * This class provides a standards-compliant implementation of URI objects
 * with support for all components defined in the RFC 3986 specification.
 * 
 * Empty URI Behavior:
 * An empty URI string ('') is a valid reference according to RFC 3986 and
 * serves specific purposes in different contexts:
 * 
 * - It represents a relative reference to the current document or context
 * - All URI components (scheme, authority, path, query, fragment) are undefined or empty
 * - Functions as a self-reference within documents (like an empty href attribute in HTML)
 * - Can serve as a base URI for resolving other relative references
 * - Provides a suitable default value when initializing URI objects
 * - Acts as a placeholder when programmatically building URIs
 * 
 * When working with empty URIs, the implementation treats them as properly formed
 * relative references with all components being empty strings or null values.
 * 
 * @link https://tools.ietf.org/html/rfc3986 RFC 3986: Uniform Resource Identifier
 */
class Uri implements UriInterface
{
    /** @var string URI scheme (e.g., http or https) */
    private $scheme = '';

    /** @var string URI user information (e.g., username:password) */
    private $userInfo = '';

    /** @var string URI host */
    private $host = '';

    /** @var int|null URI port */
    private $port;

    /** @var string URI path */
    private $path = '';

    /** @var string URI query string */
    private $query = '';

    /** @var string URI fragment */
    private $fragment = '';

    /** @var array Standard/default port numbers for various schemes */
    private static $standardPorts = [
        'http'  => 80,
        'https' => 443,
        'ftp'   => 21,
    ];

    /**
     * Creates a new URI instance.
     *
     * @param string $uri The URI string
     */
    public function __construct($uri = '')
    {
        if ($uri !== '') {
            $parts = parse_url($uri);
            if ($parts === false) {
                throw new InvalidArgumentException('Unable to parse URI: ' . $uri);
            }

            $this->scheme = isset($parts['scheme']) ? $this->normalizeScheme($parts['scheme']) : '';
            $this->userInfo = isset($parts['user']) ? $parts['user'] : '';
            if (isset($parts['pass'])) {
                $this->userInfo .= ':' . $parts['pass'];
            }
            $this->host = isset($parts['host']) ? $this->normalizeHost($parts['host']) : '';
            $this->port = isset($parts['port']) ? $this->normalizePort($parts['port'], $this->scheme) : null;
            $this->path = isset($parts['path']) ? $this->normalizePath($parts['path']) : '';
            $this->query = isset($parts['query']) ? $this->normalizeQuery($parts['query']) : '';
            $this->fragment = isset($parts['fragment']) ? $this->normalizeFragment($parts['fragment']) : '';
        }
    }

    /**
     * Normalize a URI scheme.
     *
     * @param string $scheme The scheme to normalize
     * @return string The normalized scheme
     */
    private function normalizeScheme($scheme)
    {
        return strtolower($scheme);
    }

    /**
     * Normalize a URI host.
     *
     * @param string $host The host to normalize
     * @return string The normalized host
     */
    private function normalizeHost($host)
    {
        return strtolower($host);
    }

    /**
     * Normalize a URI port.
     *
     * @param int $port The port to normalize
     * @param string $scheme The URI scheme
     * @return int|null The normalized port
     */
    private function normalizePort($port, $scheme)
    {
        if (isset(self::$standardPorts[$scheme]) && $port === self::$standardPorts[$scheme]) {
            return null;
        }
        return $port;
    }

    /**
     * Normalize a URI path.
     *
     * @param string $path The path to normalize
     * @return string The normalized path
     */
    private function normalizePath($path)
    {
        if (empty($path)) {
            return '';
        }

        // Only add a leading slash for absolute paths when there's a host
        // For relative paths or URIs without authority (mailto:, news:), preserve the original path
        if ($path[0] !== '/' && !empty($this->host)) {
            return '/' . $path;
        }

        return $path;
    }

    /**
     * Normalize a URI query.
     *
     * @param string $query The query to normalize
     * @return string The normalized query
     */
    private function normalizeQuery($query)
    {
        if (empty($query)) {
            return '';
        }

        // Remove the leading '?' if present
        if ($query[0] === '?') {
            return substr($query, 1);
        }

        return $query;
    }

    /**
     * Normalize a URI fragment.
     *
     * @param string $fragment The fragment to normalize
     * @return string The normalized fragment
     */
    private function normalizeFragment($fragment)
    {
        if (empty($fragment)) {
            return '';
        }

        // Remove the leading '#' if present
        if ($fragment[0] === '#') {
            return substr($fragment, 1);
        }

        return $fragment;
    }

    /**
     * @inheritdoc
     */
    public function getScheme()
    {
        return $this->scheme;
    }

    /**
     * @inheritdoc
     */
    public function getAuthority()
    {
        if (empty($this->host)) {
            return '';
        }

        $authority = $this->host;
        if (!empty($this->userInfo)) {
            $authority = $this->userInfo . '@' . $authority;
        }

        if ($this->port !== null) {
            $authority .= ':' . $this->port;
        }

        return $authority;
    }

    /**
     * @inheritdoc
     */
    public function getUserInfo()
    {
        return $this->userInfo;
    }

    /**
     * @inheritdoc
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @inheritdoc
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @inheritdoc
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @inheritdoc
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @inheritdoc
     */
    public function getFragment()
    {
        return $this->fragment;
    }

    /**
     * @inheritdoc
     */
    public function withScheme($scheme)
    {
        $scheme = $this->normalizeScheme($scheme);
        if ($this->scheme === $scheme) {
            return $this;
        }

        $new = clone $this;
        $new->scheme = $scheme;
        $new->port = $this->normalizePort($this->port, $scheme);

        return $new;
    }

    /**
     * @inheritdoc
     */
    public function withUserInfo($user, $password = null)
    {
        $userInfo = $user;
        if ($password !== null && $password !== '') {
            $userInfo .= ':' . $password;
        }

        if ($this->userInfo === $userInfo) {
            return $this;
        }

        $new = clone $this;
        $new->userInfo = $userInfo;

        return $new;
    }

    /**
     * @inheritdoc
     */
    public function withHost($host)
    {
        $host = $this->normalizeHost($host);
        if ($this->host === $host) {
            return $this;
        }

        $new = clone $this;
        $new->host = $host;

        return $new;
    }

    /**
     * @inheritdoc
     */
    public function withPort($port)
    {
        if ($port !== null) {
            $port = (int) $port;
            if ($port < 1 || $port > 65535) {
                throw new InvalidArgumentException(
                    sprintf('Invalid port: %d. Must be between 1 and 65535', $port)
                );
            }
        }

        $port = $this->normalizePort($port, $this->scheme);
        if ($this->port === $port) {
            return $this;
        }

        $new = clone $this;
        $new->port = $port;

        return $new;
    }

    /**
     * @inheritdoc
     */
    public function withPath($path)
    {
        $path = $this->normalizePath($path);
        if ($this->path === $path) {
            return $this;
        }

        $new = clone $this;
        $new->path = $path;

        return $new;
    }

    /**
     * @inheritdoc
     */
    public function withQuery($query)
    {
        $query = $this->normalizeQuery($query);
        if ($this->query === $query) {
            return $this;
        }

        $new = clone $this;
        $new->query = $query;

        return $new;
    }

    /**
     * @inheritdoc
     */
    public function withFragment($fragment)
    {
        $fragment = $this->normalizeFragment($fragment);
        if ($this->fragment === $fragment) {
            return $this;
        }

        $new = clone $this;
        $new->fragment = $fragment;

        return $new;
    }

    /**
     * @inheritdoc
     */
    public function __toString()
    {
        $uri = '';

        if ($this->scheme !== '') {
            $uri .= $this->scheme . ':';
        }

        $authority = $this->getAuthority();
        if ($authority !== '' || $this->scheme === 'file') {
            $uri .= '//' . $authority;
        }

        $uri .= $this->path;

        if ($this->query !== '') {
            $uri .= '?' . $this->query;
        }

        if ($this->fragment !== '') {
            $uri .= '#' . $this->fragment;
        }

        return $uri;
    }
}
