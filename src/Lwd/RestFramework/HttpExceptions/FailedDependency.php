<?php

namespace Lwd\RestFramework\HttpExceptions;

/**
 * The request failed due to failure of a previous request.
 */
class FailedDependency extends BaseHttpClientException
{
    const STATUS_CODE = 424;
    const REASON_PHRASE = 'Failed Dependency';
}
