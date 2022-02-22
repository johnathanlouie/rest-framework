<?php

namespace Lwd\RestFramework;

/**
 * Response to the HTTP request.
 */
class HttpResponse
{
    /**
     * Responds with a JSON object.
     * 
     * @param mixed $data Any JSON encodable object.
     * @param int $statusCode HTTP response status code.
     * @return void
     */
    public function sendJson($data, $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        print json_encode($data);
    }
}
