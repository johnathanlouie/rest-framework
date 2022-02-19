<?php

namespace Lwd\RestFramework\Exceptions;

/**
 * The request was well-formed but was unable to be followed due to semantic errors.
 */
class UnprocessableEntity extends BaseHttpException
{
    const STATUS_CODE = 422;
}
