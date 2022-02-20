<?php

namespace Lwd\RestFramework\HttpExceptions;

/**
 * The request failed due to failure of a previous request.
 */
class FailedDependency extends BaseHttpException
{
    const STATUS_CODE = 424;
}
