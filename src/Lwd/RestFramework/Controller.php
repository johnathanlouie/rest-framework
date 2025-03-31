<?php

namespace Lwd\RestFramework;

use Lwd\Http\Message\ResponseInterface;
use Lwd\Http\Message\ResponseFactoryInterface;
use Lwd\Http\Message\ServerRequestInterface;
use Lwd\Http\Server\RequestHandlerInterface;
use Lwd\RestFramework\HttpExceptions\BaseHttpException;
use Lwd\RestFramework\HttpExceptions\MethodNotAllowed;
use Lwd\RestFramework\HttpExceptions\NotImplemented;

/**
 * PSR-compliant base controller.
 *
 * This class implements the PSR-15 RequestHandlerInterface and serves as the foundation for 
 * all controllers in the framework. It delegates HTTP verb-specific handling (GET, POST, etc.)
 * to dedicated methods. Each method accepts a ServerRequestInterface and a mutable ResponseInterface
 * (initially created via a ResponseFactoryInterface) and should return a finalized instance of 
 * ResponseInterface, constructed according to PSR-7/PSR-17 guidelines.
 *
 * Controllers extending this base class must override these methods to implement application-specific logic.
 *
 * @package Lwd\RestFramework
 */
class Controller implements RequestHandlerInterface
{
    /** @var ResponseFactoryInterface */
    private $responseFactory;

    /**
     * Constructs the controller.
     */
    final public function __construct($responseFactory)
    {
        $this->responseFactory = $responseFactory;
    }

    /**
     * Sets the initial state of the controller.
     * 
     * @return void
     */
    public function init() {}

    /**
     * @inheritDoc
     */
    public function handle($request)
    {
        $response = $this->responseFactory->createResponse();
        $method = $request->getMethod();
        // TODO: Handle middleware.

        switch ($method) {
            case HttpMethods::GET:
                return $this->get($request, $response);
            case HttpMethods::HEAD:
                return $this->head($request, $response);
            case HttpMethods::POST:
                return $this->post($request, $response);
            case HttpMethods::PUT:
                return $this->put($request, $response);
            case HttpMethods::DELETE:
                return $this->delete($request, $response);
            case HttpMethods::CONNECT:
                return $this->connect($request, $response);
            case HttpMethods::OPTIONS:
                return $this->options($request, $response);
            case HttpMethods::TRACE:
                return $this->trace($request, $response);
            case HttpMethods::PATCH:
                return $this->patch($request, $response);
            default:
                throw new MethodNotAllowed();
        }
    }

    /**
     * Executes the GET route.
     * 
     * Retrieves data from the server. This method is safe and idempotent, used to fetch state or information about a resource.
     *
     * This method receives a blank PSR‑7 HTTP response and is expected to populate and return a finalized response.
     *
     * @param ServerRequestInterface $request HTTP request data.
     * @param ResponseInterface $response An empty response instance.
     * @return ResponseInterface A populated HTTP response instance per PSR‑7/PSR‑17.
     * @throws BaseHttpException Alternative way of creating an error response.
     */
    public function get($request, $response)
    {
        throw new NotImplemented();
    }

    /**
     * Executes the HEAD route.
     * 
     * Retrieves only the headers of a resource without its body.
     * This is useful for obtaining metadata about the resource.
     *
     * This method receives a blank PSR‑7 HTTP response and is expected to populate and return a finalized response.
     *
     * @param ServerRequestInterface $request HTTP request data.
     * @param ResponseInterface $response An empty response instance.
     * @return ResponseInterface A populated HTTP response instance per PSR‑7/PSR‑17.
     * @throws BaseHttpException Alternative way of creating an error response.
     */
    public function head($request, $response)
    {
        throw new NotImplemented();
    }

    /**
     * Executes the POST route.
     * 
     * Sends data to the server to create a new resource.
     * This method is not idempotent; multiple calls may create duplicate resources.
     *
     * This method receives a blank PSR‑7 HTTP response and is expected to populate and return a finalized response.
     *
     * @param ServerRequestInterface $request HTTP request data.
     * @param ResponseInterface $response An empty response instance.
     * @return ResponseInterface A populated HTTP response instance per PSR‑7/PSR‑17.
     * @throws BaseHttpException Alternative way of creating an error response.
     */
    public function post($request, $response)
    {
        throw new NotImplemented();
    }

    /**
     * Executes the PUT route.
     * 
     * Fully replaces an existing resource with the provided data.
     * This method is idempotent, meaning multiple identical requests produce the same result.
     *
     * This method receives a blank PSR‑7 HTTP response and is expected to populate and return a finalized response.
     *
     * @param ServerRequestInterface $request HTTP request data.
     * @param ResponseInterface $response An empty response instance.
     * @return ResponseInterface A populated HTTP response instance per PSR‑7/PSR‑17.
     * @throws BaseHttpException Alternative way of creating an error response.
     */
    public function put($request, $response)
    {
        throw new NotImplemented();
    }

    /**
     * Executes the DELETE route.
     * 
     * Removes the specified resource from the server.
     * This operation is idempotent.
     *
     * This method receives a blank PSR‑7 HTTP response and is expected to populate and return a finalized response.
     *
     * @param ServerRequestInterface $request HTTP request data.
     * @param ResponseInterface $response An empty response instance.
     * @return ResponseInterface A populated HTTP response instance per PSR‑7/PSR‑17.
     * @throws BaseHttpException Alternative way of creating an error response.
     */
    public function delete($request, $response)
    {
        throw new NotImplemented();
    }

    /**
     * Executes the CONNECT route.
     * 
     * Establishes a tunnel to the server identified by the target resource.
     * It is less common in typical RESTful API interactions.
     *
     * This method receives a blank PSR‑7 HTTP response and is expected to populate and return a finalized response.
     *
     * @param ServerRequestInterface $request HTTP request data.
     * @param ResponseInterface $response An empty response instance.
     * @return ResponseInterface A populated HTTP response instance per PSR‑7/PSR‑17.
     * @throws BaseHttpException Alternative way of creating an error response.
     */
    public function connect($request, $response)
    {
        throw new NotImplemented();
    }

    /**
     * Executes the OPTIONS route.
     * 
     * Describes all the communication options available for the target resource.
     * Often used to determine allowed methods or perform CORS checks.
     *
     * This method receives a blank PSR‑7 HTTP response and is expected to populate and return a finalized response.
     *
     * @param ServerRequestInterface $request HTTP request data.
     * @param ResponseInterface $response An empty response instance.
     * @return ResponseInterface A populated HTTP response instance per PSR‑7/PSR‑17.
     * @throws BaseHttpException Alternative way of creating an error response.
     */
    public function options($request, $response)
    {
        throw new NotImplemented();
    }

    /**
     * Executes the TRACE route.
     * 
     * Echoes back the received request for diagnostic purposes.
     * This allows the client to view what (if any) alterations or intermediaries have modified the request.
     *
     * This method receives a blank PSR‑7 HTTP response and is expected to populate and return a finalized response.
     *
     * @param ServerRequestInterface $request HTTP request data.
     * @param ResponseInterface $response An empty response instance.
     * @return ResponseInterface A populated HTTP response instance per PSR‑7/PSR‑17.
     * @throws BaseHttpException Alternative way of creating an error response.
     */
    public function trace($request, $response)
    {
        throw new NotImplemented();
    }

    /**
     * Executes the PATCH route.
     * 
     * Applies partial modifications to a resource.
     * Only specific fields within the resource are updated, rather than replacing it entirely.
     *
     * This method receives a blank PSR‑7 HTTP response and is expected to populate and return a finalized response.
     *
     * @param ServerRequestInterface $request HTTP request data.
     * @param ResponseInterface $response An empty response instance.
     * @return ResponseInterface A populated HTTP response instance per PSR‑7/PSR‑17.
     * @throws BaseHttpException Alternative way of creating an error response.
     */
    public function patch($request, $response)
    {
        throw new NotImplemented();
    }
}
