<?php

namespace Lwd\RestFramework\HttpExceptions;

/**
 * This is similar to 401 Unauthorized but authentication is needed to be done by a proxy.
 */
class ProxyAuthenticationRequired extends BaseHttpException
{
    const STATUS_CODE = 401;
}
