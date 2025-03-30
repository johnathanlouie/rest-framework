<?php

namespace Lwd\RestFramework\HttpExceptions;

/**
 * The server is unwilling to process the request because its header fields are too large. The request may be resubmitted after reducing the size of the request header fields.
 */
class RequestHeaderFieldsTooLarge extends BaseHttpClientException
{
    const STATUS_CODE = 431;
    const REASON_PHRASE = 'Request Header Fields Too Large';
}
