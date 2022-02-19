<?php

namespace Lwd\RestFramework\Exceptions;

/**
 * Although the HTTP standard specifies "unauthorized", semantically this response means "unauthenticated". That is, the client must authenticate itself to get the requested response.
 */
class Unauthenticated extends BaseHttpException
{
    const STATUS_CODE = 401;
}
