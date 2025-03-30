<?php

namespace Lwd\RestFramework\HttpExceptions;

/**
 * The client does not have access rights to the content; that is, it is unauthorized, so the server is refusing to give the requested resource. Unlike 401 Unauthorized, the client's identity is known to the server.
 *
 * The HTTP 403 Forbidden client error response status code indicates that the server understood the request but refused to process it. This status is similar to 401, except that for 403 Forbidden responses, authenticating or re-authenticating makes no difference. The request failure is tied to application logic, such as insufficient permissions to a resource or action.
 *
 * Clients that receive a 403 response should expect that repeating the request without modification will fail with the same error. Server owners may decide to send a 404 response instead of a 403 if acknowledging the existence of a resource to clients with insufficient privileges is not desired.
 */
class Forbidden extends BaseHttpClientException
{
    const STATUS_CODE = 403;
    const REASON_PHRASE = 'Forbidden';
}
