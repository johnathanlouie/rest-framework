<?php

namespace Lwd\RestFramework\Exceptions;

/**
 * The request failed due to failure of a previous request.
 */
class FailedDependency extends BaseHttpException
{
    const STATUS_CODE = 424;
}
