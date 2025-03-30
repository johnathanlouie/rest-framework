<?php

namespace Lwd\RestFramework\HttpExceptions;

/**
 * Further extensions to the request are required for the server to fulfill it.
 */
class NotExtended extends BaseHttpServerException
{
    const STATUS_CODE = 510;
    const REASON_PHRASE = 'Not Extended';
}
