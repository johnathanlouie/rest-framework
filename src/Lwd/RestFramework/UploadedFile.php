<?php

namespace Lwd\RestFramework;

use Lwd\Http\Message\StreamInterface;
use Lwd\Http\Message\UploadedFileInterface;
use InvalidArgumentException;
use RuntimeException;

/**
 * Implementation of a file uploaded through an HTTP request.
 */
class UploadedFile implements UploadedFileInterface
{
    /** @var string|null Filename provided by client */
    private $clientFilename;

    /** @var string|null Media type provided by client */
    private $clientMediaType;

    /** @var int|null Error status for the uploaded file */
    private $error;

    /** @var string|null Path to file on disk */
    private $file;

    /** @var bool Whether the file has been moved */
    private $moved = false;

    /** @var int|null Size of the uploaded file */
    private $size;

    /** @var StreamInterface|null Uploaded file as a stream */
    private $stream;

    /**
     * Constructs a new uploaded file instance.
     *
     * @param StreamInterface|string $streamOrFile The uploaded file as stream or path to file
     * @param int|null $size The size of the file in bytes
     * @param int $error The PHP file upload error code
     * @param string|null $clientFilename The filename as provided by the client
     * @param string|null $clientMediaType The media type as provided by the client
     */
    public function __construct(
        $streamOrFile,
        $size = null,
        $error = UPLOAD_ERR_OK,
        $clientFilename = null,
        $clientMediaType = null
    ) {
        $this->error = $error;
        $this->size = $size;
        $this->clientFilename = $clientFilename;
        $this->clientMediaType = $clientMediaType;

        if (is_string($streamOrFile)) {
            $this->file = $streamOrFile;
        } elseif ($streamOrFile instanceof StreamInterface) {
            $this->stream = $streamOrFile;
        } else {
            throw new InvalidArgumentException(
                'Invalid stream or file provided for UploadedFile'
            );
        }
    }

    /**
     * Validates and returns a PHP stream resource for an uploaded file.
     *
     * @return resource The PHP stream resource
     * @throws RuntimeException If the uploaded file is already moved or has an upload error
     */
    private function validateActive()
    {
        if ($this->error !== UPLOAD_ERR_OK) {
            throw new RuntimeException('Cannot retrieve stream due to upload error');
        }

        if ($this->moved) {
            throw new RuntimeException('Cannot retrieve stream after it has been moved');
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @inheritdoc
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @inheritdoc
     */
    public function getClientFilename()
    {
        return $this->clientFilename;
    }

    /**
     * @inheritdoc
     */
    public function getClientMediaType()
    {
        return $this->clientMediaType;
    }

    /**
     * @inheritdoc
     */
    public function moveTo($targetPath)
    {
        $this->validateActive();

        if ($targetPath === '') {
            throw new InvalidArgumentException(
                'Invalid target path; must be a non-empty string'
            );
        }

        if ($this->file) {
            $this->moveFile($targetPath);
        } else {
            $this->moveStream($targetPath);
        }

        $this->moved = true;
    }

    /**
     * Move a file to a new location.
     *
     * @param string $targetPath Path to which to move the uploaded file
     * @throws RuntimeException If the operation failed
     * @return void
     */
    private function moveFile($targetPath)
    {
        if (!is_file($this->file)) {
            throw new RuntimeException('Source file does not exist');
        }

        $success = copy($this->file, $targetPath);
        if (!$success) {
            throw new RuntimeException(
                sprintf('Failed to move uploaded file to %s', $targetPath)
            );
        }

        if (is_uploaded_file($this->file)) {
            unlink($this->file);
        }
    }

    /**
     * Move a stream to a new location.
     *
     * @param string $targetPath Path to which to move the uploaded file
     * @throws RuntimeException If the operation failed
     * @return void
     */
    private function moveStream($targetPath)
    {
        $source = $this->getStream();
        $handle = fopen($targetPath, 'w');

        if (!$handle) {
            throw new RuntimeException(
                sprintf('Unable to open target file: %s', $targetPath)
            );
        }

        $source->rewind();
        while (!$source->eof()) {
            fwrite($handle, $source->read(4096));
        }
        fclose($handle);
    }

    /**
     * @inheritdoc
     */
    public function getStream()
    {
        $this->validateActive();

        if ($this->stream instanceof StreamInterface) {
            return $this->stream;
        }

        if ($this->file !== null) {
            $resource = fopen($this->file, 'r');
            if ($resource === false) {
                throw new RuntimeException(
                    sprintf('Unable to open file: %s', $this->file)
                );
            }
            $this->stream = new Stream($resource);
            return $this->stream;
        }

        throw new RuntimeException('No file or stream available');
    }
}
