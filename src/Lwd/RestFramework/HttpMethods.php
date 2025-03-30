<?php

namespace Lwd\RestFramework;

/**
 * HTTP methods constants.
 */
abstract class HttpMethods
{
    /** @var string The GET method requests a representation of the specified resource. Requests using GET should only retrieve data. */
    const GET = 'GET';

    /** @var string The HEAD method asks for a response identical to a GET request, but without the response body. */
    const HEAD = 'HEAD';

    /** @var string The POST method submits an entity to the specified resource, often causing a change in state or side effects on the server. */
    const POST = 'POST';

    /** @var string The PUT method replaces all current representations of the target resource with the request payload. */
    const PUT = 'PUT';

    /** @var string The DELETE method deletes the specified resource. */
    const DELETE = 'DELETE';

    /** @var string The CONNECT method establishes a tunnel to the server identified by the target resource. */
    const CONNECT = 'CONNECT';

    /** @var string The OPTIONS method describes the communication options for the target resource. */
    const OPTIONS = 'OPTIONS';

    /** @var string The TRACE method performs a message loop-back test along the path to the target resource. */
    const TRACE = 'TRACE';

    /** @var string The PATCH method applies partial modifications to a resource. */
    const PATCH = 'PATCH';

    /**
     * Returns an array of standard HTTP methods.
     *
     * @return string[] Array of HTTP methods.
     * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Methods
     */
    public static function getMethods()
    {
        return [
            self::GET,
            self::HEAD,
            self::POST,
            self::PUT,
            self::DELETE,
            self::CONNECT,
            self::OPTIONS,
            self::TRACE,
            self::PATCH,
        ];
    }

    /**
     * Checks if the given method is a valid HTTP method.
     *
     * @param string $method HTTP method to check.
     * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Methods
     * @return boolean
     */
    public static function isValidMethod($method)
    {
        return in_array($method, self::getMethods(), false);
    }
}
