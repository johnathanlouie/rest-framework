<?php

namespace Lwd\RestFramework\Exceptions;

/**
 * The server is unwilling to process the request because its header fields are too large. The request may be resubmitted after reducing the size of the request header fields.
 */
class RequestHeaderFieldsTooLarge extends BaseHttpException
{
    const STATUS_CODE = 431;
}
