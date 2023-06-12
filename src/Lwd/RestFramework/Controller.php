<?php

namespace Lwd\RestFramework;

use Lwd\RestFramework\HttpExceptions\BaseHttpException;
use Lwd\RestFramework\HttpExceptions\NotImplemented;

/**
 * Controller is the base class for all route controllers.
 */
class Controller
{
    /**
     * Constructs the controller.
     */
    final public function __construct()
    {
    }

    /**
     * Sets the initial state of the controller.
     * 
     * @return void
     */
    public function init()
    {
    }

    /**
     * Executes the GET route.
     * 
     * @param HttpRequest $request HTTP request data.
     * @return mixed HTTP response data. Null is empty body.
     * @throws BaseHttpException If something went wrong.
     */
    public function get($request)
    {
        throw new NotImplemented();
    }

    /**
     * Executes the HEAD route.
     * 
     * @param HttpRequest $request HTTP request data.
     * @return mixed HTTP response data. Null is empty body.
     * @throws BaseHttpException If something went wrong.
     */
    public function head($request)
    {
        throw new NotImplemented();
    }

    /**
     * Executes the POST route.
     * 
     * @param HttpRequest $request HTTP request data.
     * @return mixed HTTP response data. Null is empty body.
     * @throws BaseHttpException If something went wrong.
     */
    public function post($request)
    {
        throw new NotImplemented();
    }

    /**
     * Executes the PUT route.
     * 
     * @param HttpRequest $request HTTP request data.
     * @return mixed HTTP response data. Null is empty body.
     * @throws BaseHttpException If something went wrong.
     */
    public function put($request)
    {
        throw new NotImplemented();
    }

    /**
     * Executes the DELETE route.
     * 
     * @param HttpRequest $request HTTP request data.
     * @return mixed HTTP response data. Null is empty body.
     * @throws BaseHttpException If something went wrong.
     */
    public function delete($request)
    {
        throw new NotImplemented();
    }

    /**
     * Executes the CONNECT route.
     * 
     * @param HttpRequest $request HTTP request data.
     * @return mixed HTTP response data. Null is empty body.
     * @throws BaseHttpException If something went wrong.
     */
    public function connect($request)
    {
        throw new NotImplemented();
    }

    /**
     * Executes the OPTIONS route.
     * 
     * @param HttpRequest $request HTTP request data.
     * @return mixed HTTP response data. Null is empty body.
     * @throws BaseHttpException If something went wrong.
     */
    public function options($request)
    {
        throw new NotImplemented();
    }

    /**
     * Executes the TRACE route.
     * 
     * @param HttpRequest $request HTTP request data.
     * @return mixed HTTP response data. Null is empty body.
     * @throws BaseHttpException If something went wrong.
     */
    public function trace($request)
    {
        throw new NotImplemented();
    }

    /**
     * Executes the PATCH route.
     * 
     * @param HttpRequest $request HTTP request data.
     * @return mixed HTTP response data. Null is empty body.
     * @throws BaseHttpException If something went wrong.
     */
    public function patch($request)
    {
        throw new NotImplemented();
    }
}
