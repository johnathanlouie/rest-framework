<?php

namespace Lwd\RestFramework\HttpExceptions;

/**
 * This response code means the expectation indicated by the Expect request header field cannot be met by the server.
 */
class ExpectationFailed extends BaseHttpClientException
{
    const STATUS_CODE = 417;
    const REASON_PHRASE = 'Expectation Failed';
}
