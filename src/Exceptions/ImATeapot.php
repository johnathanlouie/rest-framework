<?php

namespace Lwd\RestFramework\Exceptions;

/**
 * The server refuses the attempt to brew coffee with a teapot.
 */
class ImATeapot extends BaseHttpException
{
    const STATUS_CODE = 418;
}
