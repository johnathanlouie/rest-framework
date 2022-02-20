<?php

namespace Lwd\RestFramework\HttpExceptions;

/**
 * The server detected an infinite loop while processing the request.
 */
class LoopDetected extends BaseHttpException
{
    const STATUS_CODE = 508;
}
