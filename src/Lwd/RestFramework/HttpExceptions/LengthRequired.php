<?php

namespace Lwd\RestFramework\HttpExceptions;

/**
 * Server rejected the request because the Content-Length header field is not defined and the server requires it.
 */
class LengthRequired extends BaseHttpClientException
{
    const STATUS_CODE = 411;
    const REASON_PHRASE = 'Length Required';
}
