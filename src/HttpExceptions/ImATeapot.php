<?php

namespace Lwd\RestFramework\HttpExceptions;

/**
 * The server refuses the attempt to brew coffee with a teapot.
 */
class ImATeapot extends BaseHttpException
{
    const STATUS_CODE = 418;
}
