<?php

namespace Lwd\RestFramework\HttpExceptions;

/**
 * The server can not find the requested resource. In the browser, this means the URL is not recognized. In an API, this can also mean that the endpoint is valid but the resource itself does not exist. Servers may also send this response instead of 403 Forbidden to hide the existence of a resource from an unauthorized client. This response code is probably the most well known due to its frequent occurrence on the web.
 */
class NotFound extends BaseHttpClientException
{
    const STATUS_CODE = 404;
    const REASON_PHRASE = 'Not Found';
}
