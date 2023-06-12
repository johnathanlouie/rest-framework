<?php

namespace Lwd\RestFramework\HttpExceptions;

/**
 * The server has encountered a situation it does not know how to handle.
 */
class InternalServerError extends BaseHttpException
{
    const STATUS_CODE = 500;
}
