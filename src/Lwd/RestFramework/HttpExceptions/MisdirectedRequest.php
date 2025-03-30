<?php

namespace Lwd\RestFramework\HttpExceptions;

/**
 * The request was directed at a server that is not able to produce a response. This can be sent by a server that is not configured to produce responses for the combination of scheme and authority that are included in the request URI.
 */
class MisdirectedRequest extends BaseHttpClientException
{
    const STATUS_CODE = 421;
    const REASON_PHRASE = 'Misdirected Request';
}
