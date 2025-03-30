<?php

namespace Lwd\RestFramework\HttpExceptions;

use Lwd\RestFramework\BaseException;

/**
 * BaseHttpException is the base class for all HTTP exceptions.
 * Represents external-facing HTTP error responses.
 */
abstract class BaseHttpException extends BaseException
{
    const STATUS_CODE = 500;
    const REASON_PHRASE = 'Internal Server Error';

    /** @var mixed $body A null body is empty. */
    private $body = null;

    /** @var string|null $reasonPhrase The HTTP reason phrase. */
    private $reasonPhrase = null;

    /**
     * Sets the HTTP response body.
     * 
     * @param mixed $body The unserialized HTTP response body to set. If null, the body will be empty.
     * @return void
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * Gets the HTTP response body.
     * 
     * @return mixed The unserialized HTTP response body. Null is empty.
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Sets a custom reason phrase.
     *
     * @param string|null $reasonPhrase Custom reason phrase. If null, the default reason phrase will be used.
     * @return void
     */
    public function setReasonPhrase($reasonPhrase)
    {
        $this->reasonPhrase = $reasonPhrase;
    }

    /**
     * Gets the HTTP reason phrase.
     *
     * @return string Custom or default HTTP reason phrase.
     */
    public function getReasonPhrase()
    {
        return $this->reasonPhrase ?: static::REASON_PHRASE;
    }
}
