<?php

namespace Lwd\RestFramework\HttpExceptions;

/**
 * This error response is given when the server is acting as a gateway and cannot get a response in time.
 */
class GatewayTimeout extends BaseHttpException
{
    const STATUS_CODE = 504;
}
