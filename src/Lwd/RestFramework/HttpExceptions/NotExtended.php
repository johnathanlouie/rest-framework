<?php

namespace Lwd\RestFramework\HttpExceptions;

/**
 * Further extensions to the request are required for the server to fulfill it.
 */
class NotExtended extends BaseHttpException
{
    const STATUS_CODE = 510;
}
