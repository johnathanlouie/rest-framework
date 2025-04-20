<?php

namespace Lwd\RestFramework;

use Lwd\Http\Message\ResponseInterface;
use Lwd\Http\Message\ResponseFactoryInterface;
use Lwd\Http\Message\ServerRequestInterface;
use Lwd\Http\Server\RequestHandlerInterface;

/**
 * A simple Hello World controller.
 *
 * This controller responds to all requests with a "Hello, World!" response.
 * It implements the PSR-15 RequestHandlerInterface.
 */
class HelloWorldController extends Controller implements RequestHandlerInterface
{
    /** @var ResponseFactoryInterface */
    private $responseFactory;

    /**
     * Constructs the controller with a response factory.
     *
     * @param ResponseFactoryInterface $responseFactory Factory to create response objects
     */
    public function __construct($responseFactory)
    {
        $this->responseFactory = $responseFactory;
    }

    /**
     * Handles a server request and produces a response.
     *
     * @param ServerRequestInterface $request The server request
     * @return ResponseInterface The response
     */
    public function handle($request)
    {
        // Create a response
        $response = $this->responseFactory->createResponse(200);

        // Get a writable body stream
        $body = $response->getBody();
        $body->write('Hello, World!');

        // Set content type header
        $response = $response->withHeader('Content-Type', 'text/plain');

        return $response;
    }
}
