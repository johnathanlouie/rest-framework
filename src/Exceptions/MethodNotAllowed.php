<?php

namespace Lwd\RestFramework\Exceptions;

/**
 * The request method is known by the server but is not supported by the target resource. For example, an API may not allow calling DELETE to remove a resource.
 */
class MethodNotAllowed extends BaseHttpException
{
    const STATUS_CODE = 405;
}
