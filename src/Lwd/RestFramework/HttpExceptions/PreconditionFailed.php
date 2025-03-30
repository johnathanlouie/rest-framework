<?php

namespace Lwd\RestFramework\HttpExceptions;

/**
 * The client has indicated preconditions in its headers which the server does not meet.
 */
class PreconditionFailed extends BaseHttpClientException
{
    const STATUS_CODE = 412;
    const REASON_PHRASE = 'Precondition Failed';
}
