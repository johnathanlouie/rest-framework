<?php

namespace Lwd\RestFramework;

use Lwd\RestFramework\Exceptions\BaseHttpException;

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
     * Executes the route controller.
     * 
     * @param HttpRequest $request HTTP request data.
     * @return mixed HTTP response data. Null is empty body.
     * @throws BaseHttpException If something is wrong.
     */
    public function execute($request)
    {
        return null;
    }
}
