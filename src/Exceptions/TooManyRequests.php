<?php

namespace Lwd\RestFramework\Exceptions;

/**
 * The user has sent too many requests in a given amount of time ("rate limiting").
 */
class TooManyRequests extends BaseHttpException
{
    const STATUS_CODE = 429;
}
