<?php

namespace Lwd\RestFramework\Exceptions;

/**
 * The URI requested by the client is longer than the server is willing to interpret.
 */
class UriTooLong extends BaseHttpException
{
    const STATUS_CODE = 414;
}
