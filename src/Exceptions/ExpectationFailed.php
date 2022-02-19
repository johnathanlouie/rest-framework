<?php

namespace Lwd\RestFramework\Exceptions;

/**
 * This response code means the expectation indicated by the Expect request header field cannot be met by the server.
 */
class ExpectationFailed extends BaseHttpException
{
    const STATUS_CODE = 417;
}
