<?php

namespace Lwd\RestFramework\HttpExceptions;

/**
 * Base class for HTTP client exceptions. Useful for catching all client errors.
 */
abstract class BaseHttpClientException extends BaseHttpException
{
    const STATUS_CODE = 400;
    const REASON_PHRASE = 'Bad Request';
}
