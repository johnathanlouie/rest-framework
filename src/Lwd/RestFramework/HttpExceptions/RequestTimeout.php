<?php

namespace Lwd\RestFramework\HttpExceptions;

/**
 * This response is sent on an idle connection by some servers, even without any previous request by the client. It means that the server would like to shut down this unused connection. This response is used much more since some browsers, like Chrome, Firefox 27+, or IE9, use HTTP pre-connection mechanisms to speed up surfing. Also note that some servers merely shut down the connection without sending this message.
 */
class RequestTimeout extends BaseHttpClientException
{
    const STATUS_CODE = 408;
    const REASON_PHRASE = 'Request Timeout';
}
