<?php

namespace Lwd\RestFramework\HttpExceptions;

/**
 * The resource that is being accessed is locked.
 */
class Locked extends BaseHttpClientException
{
    const STATUS_CODE = 423;
    const REASON_PHRASE = 'Locked';
}
