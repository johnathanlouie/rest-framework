<?php

namespace Lwd\RestFramework;

use Lwd\Http\Message\ResponseInterface;
use Lwd\Http\Message\ResponseFactoryInterface;
use Lwd\Http\Message\ServerRequestInterface;
use Lwd\Http\Server\RequestHandlerInterface;

/**
 * A handler that always returns a 404 Not Found response.
 * 
 * This handler responds to all requests with a 404 status code,
 * with an empty body.
 */
class NotFoundHandler implements RequestHandlerInterface
{
    /** @var ResponseFactoryInterface */
    private $responseFactory;

    /**
     * Constructs the handler with a response factory.
     * 
     * @param ResponseFactoryInterface $responseFactory Factory to create response objects
     */
    public function __construct($responseFactory)
    {
        $this->responseFactory = $responseFactory;
    }

    /**
     * Handles a server request and produces a 404 response with empty body.
     * 
     * @param ServerRequestInterface $request The server request (ignored)
     * @return ResponseInterface The 404 response
     */
    public function handle($request)
    {
        // Create a response with 404 status code
        $response = $this->responseFactory->createResponse(404);

        // Set content type header (optional with empty body, but included for consistency)
        $response = $response->withHeader('Content-Type', 'text/plain');

        return $response;
    }
}
