<?php

namespace Lwd\RestFramework\HttpExceptions;

/**
 * The resource that is being accessed is locked.
 */
class Locked extends BaseHttpException
{
    const STATUS_CODE = 423;
}
