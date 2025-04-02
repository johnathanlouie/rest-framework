<?php

namespace Lwd\RestFramework;

use Lwd\Http\Message\ResponseInterface;
use Lwd\Http\Message\StreamInterface;

/**
 * Implementation of an HTTP response.
 * 
 * This class represents an HTTP response message as described in PSR-7.
 */
class Response extends Message implements ResponseInterface
{
    /** @var int HTTP status code */
    private $statusCode;

    /** @var string HTTP reason phrase */
    private $reasonPhrase;

    /**
     * Constructs a new HTTP response.
     *
     * @param int $statusCode HTTP status code
     * @param string $reasonPhrase HTTP reason phrase
     * @param array $headers Response headers, normalized according to PSR-7 requirements
     * @param StreamInterface|null $body Response body as a stream object
     * @param string $protocolVersion HTTP protocol version (e.g., "1.0", "1.1", "2.0")
     */
    public function __construct(
        $statusCode,
        $reasonPhrase,
        $headers,
        $body,
        $protocolVersion
    ) {
        parent::__construct($protocolVersion, $headers, $body);

        $this->setStatus($statusCode, $reasonPhrase);
    }

    /**
     * @inheritDoc
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @inheritDoc
     */
    public function withStatus($code, $reasonPhrase = '')
    {
        $new = clone $this;
        $new->setStatus($code, $reasonPhrase);
        return $new;
    }

    /**
     * @inheritDoc
     */
    public function getReasonPhrase()
    {
        return $this->reasonPhrase;
    }

    /**
     * Sets the status code and reason phrase.
     *
     * @param int $statusCode HTTP status code
     * @param string $reasonPhrase HTTP reason phrase
     * @throws \InvalidArgumentException For invalid status code arguments
     */
    private function setStatus($statusCode, $reasonPhrase = '')
    {
        if (!is_numeric($statusCode) || $statusCode < 100 || $statusCode > 599) {
            throw new \InvalidArgumentException('Status code must be an integer between 100 and 599');
        }

        $this->statusCode = (int) $statusCode;

        // If reason phrase is empty, use the standard reason phrase for this status code
        if ($reasonPhrase === '') {
            $reasonPhrase = $this->getStandardReasonPhrase($statusCode);
        }

        $this->reasonPhrase = $reasonPhrase;
    }

    /**
     * Get the standard reason phrase for an HTTP status code.
     *
     * @param int $code HTTP status code
     * @return string Reason phrase
     */
    private function getStandardReasonPhrase($code)
    {
        $phrases = [
            // 1xx Informational
            100 => 'Continue',
            101 => 'Switching Protocols',
            102 => 'Processing',
            103 => 'Early Hints',

            // 2xx Success
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            203 => 'Non-Authoritative Information',
            204 => 'No Content',
            205 => 'Reset Content',
            206 => 'Partial Content',
            207 => 'Multi-Status',
            208 => 'Already Reported',
            226 => 'IM Used',

            // 3xx Redirection
            300 => 'Multiple Choices',
            301 => 'Moved Permanently',
            302 => 'Found',
            303 => 'See Other',
            304 => 'Not Modified',
            305 => 'Use Proxy',
            307 => 'Temporary Redirect',
            308 => 'Permanent Redirect',

            // 4xx Client Error
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            407 => 'Proxy Authentication Required',
            408 => 'Request Timeout',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            413 => 'Payload Too Large',
            414 => 'URI Too Long',
            415 => 'Unsupported Media Type',
            416 => 'Range Not Satisfiable',
            417 => 'Expectation Failed',
            418 => 'I\'m a teapot',
            421 => 'Misdirected Request',
            422 => 'Unprocessable Entity',
            423 => 'Locked',
            424 => 'Failed Dependency',
            425 => 'Too Early',
            426 => 'Upgrade Required',
            428 => 'Precondition Required',
            429 => 'Too Many Requests',
            431 => 'Request Header Fields Too Large',
            451 => 'Unavailable For Legal Reasons',

            // 5xx Server Error
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Timeout',
            505 => 'HTTP Version Not Supported',
            506 => 'Variant Also Negotiates',
            507 => 'Insufficient Storage',
            508 => 'Loop Detected',
            510 => 'Not Extended',
            511 => 'Network Authentication Required',
        ];

        return isset($phrases[$code]) ? $phrases[$code] : '';
    }
}
