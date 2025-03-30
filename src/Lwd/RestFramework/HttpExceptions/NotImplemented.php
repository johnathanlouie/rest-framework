<?php

namespace Lwd\RestFramework\HttpExceptions;

/**
 * The request method is not supported by the server and cannot be handled. The only methods that servers are required to support (and therefore that must not return this code) are GET and HEAD.
 */
class NotImplemented extends BaseHttpServerException
{
    const STATUS_CODE = 501;
    const REASON_PHRASE = 'Not Implemented';
}
