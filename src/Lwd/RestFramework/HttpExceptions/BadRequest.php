<?php

namespace Lwd\RestFramework\HttpExceptions;

/**
 * The server cannot or will not process the request due to something that is perceived to be a client error (e.g., malformed request syntax, invalid request message framing, or deceptive request routing).
 */
class BadRequest extends BaseHttpClientException
{
    const STATUS_CODE = 400;
    const REASON_PHRASE = 'Bad Request';
}
