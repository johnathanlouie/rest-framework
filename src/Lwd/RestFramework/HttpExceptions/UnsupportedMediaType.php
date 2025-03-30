<?php

namespace Lwd\RestFramework\HttpExceptions;

/**
 * The media format of the requested data is not supported by the server, so the server is rejecting the request.
 */
class UnsupportedMediaType extends BaseHttpClientException
{
    const STATUS_CODE = 415;
    const REASON_PHRASE = 'Unsupported Media Type';
}
