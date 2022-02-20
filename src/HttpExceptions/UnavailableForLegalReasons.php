<?php

namespace Lwd\RestFramework\HttpExceptions;

/**
 * The user agent requested a resource that cannot legally be provided, such as a web page censored by a government.
 */
class UnavailableForLegalReasons extends BaseHttpException
{
    const STATUS_CODE = 451;
}
