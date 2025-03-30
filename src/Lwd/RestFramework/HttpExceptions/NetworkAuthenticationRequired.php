<?php

namespace Lwd\RestFramework\HttpExceptions;

/**
 * Indicates that the client needs to authenticate to gain network access.
 */
class NetworkAuthenticationRequired extends BaseHttpServerException
{
    const STATUS_CODE = 511;
    const REASON_PHRASE = 'Network Authentication Required';
}
