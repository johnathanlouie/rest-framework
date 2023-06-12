<?php

namespace Lwd\RestFramework\HttpExceptions;

/**
 * Indicates that the client needs to authenticate to gain network access.
 */
class NetworkAuthenticationRequired extends BaseHttpException
{
    const STATUS_CODE = 511;
}
