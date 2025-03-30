<?php

namespace Lwd\RestFramework\HttpExceptions;

/**
 * The request method is known by the server but is not supported by the target resource. For example, an API may not allow calling DELETE to remove a resource.
 */
class MethodNotAllowed extends BaseHttpClientException
{
    const STATUS_CODE = 405;
    const REASON_PHRASE = 'Method Not Allowed';
}
