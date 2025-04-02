<?php

namespace Lwd\RestFramework;

use Lwd\Http\Message\MessageInterface;
use Lwd\Http\Message\StreamInterface;

/**
 * Base implementation of HTTP messages.
 * 
 * This class provides functionality common to both requests and responses.
 */
class Message implements MessageInterface
{
    /** @var string HTTP protocol version */
    protected $protocolVersion;

    /** @var array HTTP headers */
    protected $headers;

    /** @var StreamInterface|null Message body */
    protected $body;

    /**
     * Constructs a new HTTP message.
     *
     * @param string $protocolVersion HTTP protocol version (e.g., "1.0", "1.1", "2.0")
     * @param array $headers Message headers, normalized according to PSR-7 requirements
     * @param StreamInterface|null $body Message body as a stream object
     */
    public function __construct(
        $protocolVersion,
        $headers,
        $body
    ) {
        $this->protocolVersion = $protocolVersion;
        $this->headers = $headers;
        $this->body = $body;
    }

    /**
     * @inheritDoc
     */
    public function getProtocolVersion()
    {
        return $this->protocolVersion;
    }

    /**
     * @inheritDoc
     */
    public function withProtocolVersion($version)
    {
        $new = clone $this;
        $new->protocolVersion = $version;
        return $new;
    }

    /**
     * @inheritDoc
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @inheritDoc
     */
    public function hasHeader($name)
    {
        foreach ($this->headers as $header => $values) {
            if (strtolower($header) === strtolower($name)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @inheritDoc
     */
    public function getHeader($name)
    {
        foreach ($this->headers as $header => $values) {
            if (strtolower($header) === strtolower($name)) {
                return $values;
            }
        }
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getHeaderLine($name)
    {
        $values = $this->getHeader($name);
        if (empty($values)) {
            return '';
        }
        return implode(', ', $values);
    }

    /**
     * @inheritDoc
     */
    public function withHeader($name, $value)
    {
        $new = clone $this;
        $value = is_array($value) ? $value : [$value];

        $headerFound = false;
        foreach ($new->headers as $header => $values) {
            if (strtolower($header) === strtolower($name)) {
                $new->headers[$header] = $value;
                $headerFound = true;
                break;
            }
        }

        if (!$headerFound) {
            $new->headers[$name] = $value;
        }

        return $new;
    }

    /**
     * @inheritDoc
     */
    public function withAddedHeader($name, $value)
    {
        if (!$this->hasHeader($name)) {
            return $this->withHeader($name, $value);
        }

        $new = clone $this;
        $value = is_array($value) ? $value : [$value];

        foreach ($new->headers as $header => $values) {
            if (strtolower($header) === strtolower($name)) {
                $new->headers[$header] = array_merge($values, $value);
                break;
            }
        }

        return $new;
    }

    /**
     * @inheritDoc
     */
    public function withoutHeader($name)
    {
        $new = clone $this;

        foreach ($new->headers as $header => $values) {
            if (strtolower($header) === strtolower($name)) {
                unset($new->headers[$header]);
                break;
            }
        }

        return $new;
    }

    /**
     * @inheritDoc
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @inheritDoc
     */
    public function withBody($body)
    {
        $new = clone $this;
        $new->body = $body;
        return $new;
    }
}
