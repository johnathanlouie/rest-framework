<?php

namespace Lwd\RestFramework;

use Lwd\Http\Message\ServerRequestInterface;
use Lwd\Http\Message\UriInterface;
use Lwd\Http\Message\ServerRequestFactoryInterface;

class ServerRequestFactory implements ServerRequestFactoryInterface
{
    /**
     * @inheritDoc
     */
    public function createServerRequest($method, $uri, $serverParams = [])
    {
        // Use PHP superglobals as default values
        $serverParams = !empty($serverParams) ? $serverParams : $_SERVER;

        // Get protocol version from server params or fall back to '1.1'
        $protocolVersion = isset($serverParams['SERVER_PROTOCOL'])
            ? str_replace('HTTP/', '', $serverParams['SERVER_PROTOCOL'])
            : '1.1';

        // Extract headers from server params
        $headers = [];
        foreach ($serverParams as $key => $value) {
            // Common non-HTTP_ headers - use case-insensitive comparison
            if (strcasecmp($key, 'CONTENT_TYPE') === 0) {
                $headers['Content-Type'] = $value;
            } elseif (strcasecmp($key, 'CONTENT_LENGTH') === 0) {
                $headers['Content-Length'] = $value;
            } elseif (stripos($key, 'HTTP_') === 0) {
                // Convert HTTP_HEADER_NAME to Header-Name
                $name = str_replace('_', '-', substr($key, 5));
                $headers[$name] = $value;
            }
        }

        // Extract request target from server params, accounting for proxies
        $requestTarget = null;

        // Check for proxy headers first
        if (isset($serverParams['HTTP_X_ORIGINAL_URL'])) {
            $requestTarget = $serverParams['HTTP_X_ORIGINAL_URL'];
        } elseif (isset($serverParams['HTTP_X_REWRITE_URL'])) {
            $requestTarget = $serverParams['HTTP_X_REWRITE_URL'];
        } elseif (isset($serverParams['HTTP_X_FORWARDED_URI'])) {
            $requestTarget = $serverParams['HTTP_X_FORWARDED_URI'];
        } elseif (isset($serverParams['REQUEST_URI'])) {
            $requestTarget = $serverParams['REQUEST_URI'];
        }

        // Remove query string if present to get just the path
        if ($requestTarget !== null && strpos($requestTarget, '?') !== false) {
            $requestTarget = strstr($requestTarget, '?', true);
        }

        return new ServerRequest(
            $method,
            $uri,
            $protocolVersion, // Use protocol version from server params
            $headers, // Use headers extracted from server params
            null, // Default body 
            $requestTarget, // Request target extracted from server params
            $serverParams,
            $_COOKIE, // Use cookie superglobal
            $_GET, // Use query params from $_GET
            $_POST, // Use parsed body from $_POST
            $_FILES, // Use uploaded files from $_FILES
            []  // Default attributes (no superglobal for this)
        );
    }
}
