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
        
        return new ServerRequest(
            $method,
            $uri,
            $protocolVersion, // Use protocol version from server params
            [], // Default headers
            null, // Default body 
            null, // Default request target
            $serverParams,
            $_COOKIE, // Use cookie superglobal
            $_GET, // Use query params from $_GET
            $_POST, // Use parsed body from $_POST
            $_FILES, // Use uploaded files from $_FILES
            []  // Default attributes (no superglobal for this)
        );
    }
}
