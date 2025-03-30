<?php

namespace Lwd\RestFramework\HttpExceptions;

/**
 * The request was well-formed but was unable to be followed due to semantic errors.
 */
class UnprocessableEntity extends BaseHttpClientException
{
    const STATUS_CODE = 422;
    const REASON_PHRASE = 'Unprocessable Entity';
}
