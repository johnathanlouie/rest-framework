<?php

namespace Lwd\RestFramework\HttpExceptions;

/**
 * This error response means that the server, while working as a gateway to get a response needed to handle the request, got an invalid response.
 */
class BadGateway extends BaseHttpException
{
    const STATUS_CODE = 502;
}
