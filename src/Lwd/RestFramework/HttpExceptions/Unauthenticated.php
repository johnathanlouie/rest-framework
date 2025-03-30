<?php

namespace Lwd\RestFramework\HttpExceptions;

/**
 * Although the HTTP standard specifies "unauthorized", semantically this response means "unauthenticated". That is, the client must authenticate itself to get the requested response.
 *
 * The HTTP 401 Unauthorized client error response status code indicates that a request was not successful because it lacks valid authentication credentials for the requested resource. This status code is sent with an HTTP WWW-Authenticate response header that contains information on the authentication scheme the server expects the client to include to make the request successfully.
 *
 * A 401 Unauthorized is similar to the 403 Forbidden response, except that a 403 is returned when a request contains valid credentials, but the client does not have permissions to perform a certain action.
 */
class Unauthorized extends BaseHttpClientException
{
    const STATUS_CODE = 401;
    const REASON_PHRASE = 'Unauthorized';
}
