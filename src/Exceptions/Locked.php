<?php

namespace Lwd\RestFramework\Exceptions;

/**
 * The resource that is being accessed is locked.
 */
class Locked extends BaseHttpException
{
    const STATUS_CODE = 423;
}
