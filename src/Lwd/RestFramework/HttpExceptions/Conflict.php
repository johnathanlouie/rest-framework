<?php

namespace Lwd\RestFramework\HttpExceptions;

/**
 * This response is sent when a request conflicts with the current state of the server.
 */
class Conflict extends BaseHttpException
{
    const STATUS_CODE = 409;
}
