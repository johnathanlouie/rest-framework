<?php

namespace Lwd\RestFramework\Exceptions;

/**
 * The HTTP version used in the request is not supported by the server.
 */
class HttpVersionNotSupported extends BaseHttpException
{
    const STATUS_CODE = 505;
}
