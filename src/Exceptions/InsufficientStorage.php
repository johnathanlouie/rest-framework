<?php

namespace Lwd\RestFramework\Exceptions;

/**
 * The method could not be performed on the resource because the server is unable to store the representation needed to successfully complete the request.
 */
class InsufficientStorage extends BaseHttpException
{
    const STATUS_CODE = 507;
}
