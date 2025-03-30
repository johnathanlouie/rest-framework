<?php

namespace Lwd\RestFramework\HttpExceptions;

/**
 * The server detected an infinite loop while processing the request.
 */
class LoopDetected extends BaseHttpServerException
{
    const STATUS_CODE = 508;
    const REASON_PHRASE = 'Loop Detected';
}
