<?php

namespace Lwd\RestFramework\HttpExceptions;

/**
 * The HTTP version used in the request is not supported by the server.
 */
class HttpVersionNotSupported extends BaseHttpServerException
{
    const STATUS_CODE = 505;
    const REASON_PHRASE = 'HTTP Version Not Supported';
}
