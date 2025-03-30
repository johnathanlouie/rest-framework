<?php

namespace Lwd\RestFramework\HttpExceptions;

/**
 * This is similar to 401 Unauthorized but authentication is needed to be done by a proxy.
 */
class ProxyAuthenticationRequired extends BaseHttpClientException
{
    const STATUS_CODE = 407;
    const REASON_PHRASE = 'Proxy Authentication Required';
}
