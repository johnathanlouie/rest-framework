<?php

namespace Lwd\RestFramework\Exceptions;

/**
 * Request entity is larger than limits defined by server. The server might close the connection or return an Retry-After header field.
 */
class PayloadTooLarge extends BaseHttpException
{
    const STATUS_CODE = 413;
}
