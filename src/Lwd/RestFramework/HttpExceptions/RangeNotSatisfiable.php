<?php

namespace Lwd\RestFramework\HttpExceptions;

/**
 * The range specified by the Range header field in the request cannot be fulfilled. It's possible that the range is outside the size of the target URI's data.
 */
class RangeNotSatisfiable extends BaseHttpClientException
{
    const STATUS_CODE = 416;
    const REASON_PHRASE = 'Range Not Satisfiable';
}
