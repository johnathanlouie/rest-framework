<?php

namespace Lwd\RestFramework\Exceptions;

use RuntimeException;

/**
 * BaseHttpException is the base class for all HTTP exceptions.
 */
abstract class BaseHttpException extends RuntimeException
{
    const STATUS_CODE = 500;
}
