<?php

namespace Lwd\RestFramework\HttpExceptions;

/**
 * The method could not be performed on the resource because the server is unable to store the representation needed to successfully complete the request.
 */
class InsufficientStorage extends BaseHttpServerException
{
    const STATUS_CODE = 507;
    const REASON_PHRASE = 'Insufficient Storage';
}
