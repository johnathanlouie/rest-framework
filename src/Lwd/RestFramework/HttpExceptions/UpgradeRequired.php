<?php

namespace Lwd\RestFramework\HttpExceptions;

/**
 * The server refuses to perform the request using the current protocol but might be willing to do so after the client upgrades to a different protocol. The server sends an Upgrade header in a 426 response to indicate the required protocol(s).
 */
class UpgradeRequired extends BaseHttpClientException
{
    const STATUS_CODE = 426;
    const REASON_PHRASE = 'Upgrade Required';
}
