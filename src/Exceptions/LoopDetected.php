<?php

namespace Lwd\RestFramework\Exceptions;

/**
 * The server detected an infinite loop while processing the request.
 */
class LoopDetected extends BaseHttpException
{
    const STATUS_CODE = 508;
}
