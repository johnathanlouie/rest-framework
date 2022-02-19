<?php

namespace Lwd\RestFramework\Exceptions;

/**
 * The client has indicated preconditions in its headers which the server does not meet.
 */
class PreconditionFailed extends BaseHttpException
{
    const STATUS_CODE = 412;
}
