<?php

namespace Lwd\RestFramework;

use Lwd\Http\Server\MiddlewareInterface;
use Lwd\Http\Message\ServerRequestInterface;
use Lwd\Http\Server\RequestHandlerInterface;
use Lwd\Http\Message\ResponseInterface;

/**
 * A no-operation middleware implementation.
 * 
 * This middleware performs no operation and simply delegates to the next
 * middleware in the pipeline. It can be used as a placeholder in application
 * code when middleware is required by the interface but no operation is needed.
 */
class NullMiddleware implements MiddlewareInterface
{
    /**
     * Process an incoming server request and return a response.
     *
     * This method simply delegates to the next request handler without
     * performing any operations on the request or response.
     *
     * @param ServerRequestInterface $request The server request
     * @param RequestHandlerInterface $handler The next request handler
     * @return ResponseInterface The response from the next handler
     */
    public function process($request,  $handler)
    {
        // No operation - simply pass the request to the next handler
        return $handler->handle($request);
    }
}
