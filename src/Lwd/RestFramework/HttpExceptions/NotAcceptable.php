<?php

namespace Lwd\RestFramework\HttpExceptions;

/**
 * This response is sent when the web server, after performing server-driven content negotiation, doesn't find any content that conforms to the criteria given by the user agent.
 */
class NotAcceptable extends BaseHttpException
{
    const STATUS_CODE = 406;
}
