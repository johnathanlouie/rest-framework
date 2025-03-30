<?php

namespace Lwd\RestFramework\HttpExceptions;

/**
 * Request entity is larger than limits defined by server. The server might close the connection or return an Retry-After header field.
 */
class PayloadTooLarge extends BaseHttpClientException
{
    const STATUS_CODE = 413;
    const REASON_PHRASE = 'Payload Too Large';
}
