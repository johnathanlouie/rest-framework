<?php

namespace Lwd\RestFramework;

use Lwd\Http\Message\StreamInterface;
use RuntimeException;
use InvalidArgumentException;
use Exception;

/**
 * This class provides a standard-compliant implementation of StreamInterface
 * for working with stream resources.
 */
class Stream implements StreamInterface
{
    /** @var resource|null The underlying stream resource */
    private $stream;

    /** @var bool Whether the stream is readable */
    private $readable;

    /** @var bool Whether the stream is writable */
    private $writable;

    /** @var bool Whether the stream is seekable */
    private $seekable;

    /** @var int|null The size of the stream */
    private $size;

    /** @var string|null Stream metadata */
    private $uri;

    /**
     * Creates a new stream instance.
     *
     * @param resource|string $stream A PHP stream resource or a string
     * @throws InvalidArgumentException If the stream is not a resource or cannot be converted to one
     */
    public function __construct($stream)
    {
        if (is_string($stream)) {
            $resource = fopen('php://temp', 'r+');
            if ($resource === false) {
                throw new RuntimeException('Could not create temporary stream');
            }
            fwrite($resource, $stream);
            rewind($resource);
            $stream = $resource;
        }

        if (!is_resource($stream)) {
            throw new InvalidArgumentException('Stream must be a resource');
        }

        $this->stream = $stream;
        $this->determineMetadata();
    }

    /**
     * Determines metadata properties for the stream
     * 
     * @return void
     */
    private function determineMetadata()
    {
        $meta = stream_get_meta_data($this->stream);
        $this->seekable = $meta['seekable'];
        $this->uri = isset($meta['uri']) ? $meta['uri'] : null;

        $mode = $meta['mode'];
        $this->readable = (strpos($mode, 'r') !== false || strpos($mode, '+') !== false);
        $this->writable = (strpos($mode, 'w') !== false
            || strpos($mode, 'a') !== false
            || strpos($mode, '+') !== false
            || strpos($mode, 'x') !== false
            || strpos($mode, 'c') !== false);
    }

    /**
     * @inheritDoc
     */
    public function __toString()
    {
        try {
            if ($this->isSeekable()) {
                $this->rewind();
            }
            return $this->getContents();
        } catch (Exception $e) {
            // Per PSR-7, __toString must not throw an exception
            return '';
        }
    }

    /**
     * @inheritDoc
     */
    public function close()
    {
        if ($this->stream) {
            $resource = $this->detach();
            fclose($resource);
        }
    }

    /**
     * @inheritDoc
     */
    public function detach()
    {
        if (!$this->stream) {
            return null;
        }

        $result = $this->stream;
        $this->stream = null;
        $this->size = null;
        $this->uri = null;
        $this->readable = false;
        $this->writable = false;
        $this->seekable = false;

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function getSize()
    {
        if ($this->size !== null) {
            return $this->size;
        }

        if (!$this->stream) {
            return null;
        }

        // Clear the stat cache for the stream's file descriptor
        $stats = fstat($this->stream);
        if (isset($stats['size'])) {
            $this->size = $stats['size'];
            return $this->size;
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function tell()
    {
        if (!$this->stream) {
            throw new RuntimeException('Stream is detached');
        }

        $position = ftell($this->stream);
        if ($position === false) {
            throw new RuntimeException('Unable to determine stream position');
        }

        return $position;
    }

    /**
     * @inheritDoc
     */
    public function eof()
    {
        if (!$this->stream) {
            return true;
        }

        return feof($this->stream);
    }

    /**
     * @inheritDoc
     */
    public function isSeekable()
    {
        return $this->seekable;
    }

    /**
     * @inheritDoc
     */
    public function seek($offset, $whence = SEEK_SET)
    {
        if (!$this->stream) {
            throw new RuntimeException('Stream is detached');
        }

        if (!$this->isSeekable()) {
            throw new RuntimeException('Stream is not seekable');
        }

        if (fseek($this->stream, $offset, $whence) === -1) {
            throw new RuntimeException('Unable to seek to stream position ' . $offset . ' with whence ' . var_export($whence, true));
        }
    }

    /**
     * @inheritDoc
     */
    public function rewind()
    {
        $this->seek(0);
    }

    /**
     * @inheritDoc
     */
    public function isWritable()
    {
        return $this->writable;
    }

    /**
     * @inheritDoc
     */
    public function write($string)
    {
        if (!$this->stream) {
            throw new RuntimeException('Stream is detached');
        }

        if (!$this->isWritable()) {
            throw new RuntimeException('Cannot write to a non-writable stream');
        }

        // Reset size so it will be recalculated on next call to getSize()
        $this->size = null;

        $result = fwrite($this->stream, $string);
        if ($result === false) {
            throw new RuntimeException('Unable to write to stream');
        }

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function isReadable()
    {
        return $this->readable;
    }

    /**
     * @inheritDoc
     */
    public function read($length)
    {
        if (!$this->stream) {
            throw new RuntimeException('Stream is detached');
        }

        if (!$this->isReadable()) {
            throw new RuntimeException('Cannot read from a non-readable stream');
        }

        $data = fread($this->stream, $length);
        if ($data === false) {
            throw new RuntimeException('Unable to read from stream');
        }

        return $data;
    }

    /**
     * @inheritDoc
     */
    public function getContents()
    {
        if (!$this->stream) {
            throw new RuntimeException('Stream is detached');
        }

        if (!$this->isReadable()) {
            throw new RuntimeException('Cannot read from a non-readable stream');
        }

        $contents = stream_get_contents($this->stream);
        if ($contents === false) {
            throw new RuntimeException('Unable to read stream contents');
        }

        return $contents;
    }

    /**
     * @inheritDoc
     */
    public function getMetadata($key = null)
    {
        if (!$this->stream) {
            return $key ? null : array();
        }

        $meta = stream_get_meta_data($this->stream);
        if ($key === null) {
            return $meta;
        }

        return isset($meta[$key]) ? $meta[$key] : null;
    }
}
