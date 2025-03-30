<?php

namespace Lwd\RestFramework\HttpExceptions;

/**
 * The server refuses the attempt to brew coffee with a teapot.
 */
class ImATeapot extends BaseHttpClientException
{
    const STATUS_CODE = 418;
    const REASON_PHRASE = "I'm a teapot";
}
