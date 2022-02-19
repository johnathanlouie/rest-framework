<?php

namespace Lwd\RestFramework\Exceptions;

/**
 * The media format of the requested data is not supported by the server, so the server is rejecting the request.
 */
class UnsupportedMediaType extends BaseHttpException
{
    const STATUS_CODE = 415;
}
