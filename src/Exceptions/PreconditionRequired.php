<?php

namespace Lwd\RestFramework\Exceptions;

/**
 * The origin server requires the request to be conditional. This response is intended to prevent the 'lost update' problem, where a client GETs a resource's state, modifies it and PUTs it back to the server, when meanwhile a third party has modified the state on the server, leading to a conflict.
 */
class PreconditionRequired extends BaseHttpException
{
    const STATUS_CODE = 428;
}
