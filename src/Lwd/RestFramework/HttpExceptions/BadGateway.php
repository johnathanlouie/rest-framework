<?php

namespace Lwd\RestFramework\HttpExceptions;

/**
 * This error response means that the server, while working as a gateway to get a response needed to handle the request, got an invalid response.
 */
class BadGateway extends BaseHttpServerException
{
    const STATUS_CODE = 502;
    const REASON_PHRASE = 'Bad Gateway';
}
