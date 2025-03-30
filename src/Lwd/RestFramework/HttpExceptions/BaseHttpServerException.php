<?php

namespace Lwd\RestFramework\HttpExceptions;

/**
 * Base class for HTTP server exceptions. Useful for catching all server errors.
 */
abstract class BaseHttpServerException extends BaseHttpException
{
    const STATUS_CODE = 500;
    const REASON_PHRASE = 'Internal Server Error';
}
