<?php

namespace Lwd\RestFramework\HttpExceptions;

/**
 * The URI requested by the client is longer than the server is willing to interpret.
 */
class UriTooLong extends BaseHttpClientException
{
    const STATUS_CODE = 414;
    const REASON_PHRASE = 'URI Too Long';
}
