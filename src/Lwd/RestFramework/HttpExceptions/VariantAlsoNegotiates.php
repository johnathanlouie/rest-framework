<?php

namespace Lwd\RestFramework\HttpExceptions;

/**
 * The server has an internal configuration error: the chosen variant resource is configured to engage in transparent content negotiation itself, and is therefore not a proper end point in the negotiation process.
 */
class VariantAlsoNegotiates extends BaseHttpServerException
{
    const STATUS_CODE = 506;
    const REASON_PHRASE = 'Variant Also Negotiates';
}
