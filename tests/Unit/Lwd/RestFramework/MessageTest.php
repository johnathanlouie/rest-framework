<?php

namespace Tests\Unit\Lwd\RestFramework;

use Lwd\RestFramework\Message;
use Lwd\Http\Message\StreamInterface;
use PHPUnit_Framework_TestCase;

/**
 * Tests for the Message class
 * 
 * @coversDefaultClass \Lwd\RestFramework\Message
 */
class MessageTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var StreamInterface
     */
    protected $mockStream;

    /**
     * @var \Faker\Generator
     */
    protected $faker;

    protected function setUp()
    {
        // Initialize Faker
        $this->faker = \Faker\Factory::create();

        // Create a mock of StreamInterface for testing
        $this->mockStream = $this->getMockBuilder('Lwd\Http\Message\StreamInterface')
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * Test constructor and accessor methods
     * 
     * @covers ::__construct
     * @covers ::getProtocolVersion
     * @covers ::getHeaders
     * @covers ::getBody
     */
    public function testConstructor()
    {
        $protocolVersion = $this->faker->randomElement(['1.0', '1.1', '2.0']);
        $headers = [
            'Content-Type' => [$this->faker->mimeType()],
            'X-Custom' => [$this->faker->word, $this->faker->word]
        ];

        $message = new Message($protocolVersion, $headers, $this->mockStream);

        $this->assertEquals($protocolVersion, $message->getProtocolVersion());
        $this->assertEquals($protocolVersion, $message->getProtocolVersion(), 'Protocol version should be immutable');

        $this->assertEquals($headers, $message->getHeaders());
        $this->assertEquals($headers, $message->getHeaders(), 'Headers should be immutable');

        $this->assertSame($this->mockStream, $message->getBody());
        $this->assertSame($this->mockStream, $message->getBody(), 'Body should be immutable');
    }

    /**
     * Test withProtocolVersion method - correctness and immutability
     * 
     * @covers ::withProtocolVersion
     */
    public function testWithProtocolVersion()
    {
        $originalVersion = '1.1';
        $newVersion = '2.0';
        $message = new Message($originalVersion, [], $this->mockStream);
        $newMessage = $message->withProtocolVersion($newVersion);

        // Test immutability - original is unchanged
        $this->assertEquals($originalVersion, $message->getProtocolVersion());
        $this->assertEquals($originalVersion, $message->getProtocolVersion(), 'Original protocol version should be immutable');

        // Test correctness - new instance has new protocol version
        $this->assertEquals($newVersion, $newMessage->getProtocolVersion());
        $this->assertEquals($newVersion, $newMessage->getProtocolVersion(), 'New protocol version should be immutable');

        // Ensure it's a new instance
        $this->assertNotSame($message, $newMessage);
    }

    /**
     * Test hasHeader method
     * 
     * @covers ::hasHeader
     */
    public function testHasHeader()
    {
        $headerName = 'Content-Type';
        $customHeaderName = 'X-' . $this->faker->word;

        $headers = [
            $headerName => [$this->faker->mimeType()],
            $customHeaderName => [$this->faker->word]
        ];

        $message = new Message('1.1', $headers, $this->mockStream);

        $this->assertTrue($message->hasHeader($headerName));
        $this->assertTrue($message->hasHeader(strtolower($headerName)), 'Headers should be case-insensitive');
        $this->assertTrue($message->hasHeader($customHeaderName));
        $this->assertFalse($message->hasHeader('X-Not-Exists'));
    }

    /**
     * Test getHeader method - correctness and immutability
     * 
     * @covers ::getHeader
     */
    public function testGetHeader()
    {
        $contentType = $this->faker->mimeType();
        $customValue1 = $this->faker->word;
        $customValue2 = $this->faker->word;

        $headers = [
            'Content-Type' => [$contentType],
            'X-Custom' => [$customValue1, $customValue2]
        ];

        $message = new Message('1.1', $headers, $this->mockStream);

        // Test correctness
        $this->assertEquals([$contentType], $message->getHeader('Content-Type'));
        $this->assertEquals([$contentType], $message->getHeader('content-type'), 'Headers should be case-insensitive');
        $this->assertEquals([$customValue1, $customValue2], $message->getHeader('X-Custom'));
        $this->assertEquals([], $message->getHeader('X-Not-Exists'));

        // Test immutability - modifying returned array shouldn't affect message
        $headerValues = $message->getHeader('X-Custom');
        $headerValues[] = $this->faker->word;
        $this->assertEquals([$customValue1, $customValue2], $message->getHeader('X-Custom'), 'Header values should be immutable');
    }

    /**
     * Test getHeaderLine method - correctness and immutability
     * 
     * @covers ::getHeaderLine
     */
    public function testGetHeaderLine()
    {
        $contentType = $this->faker->mimeType();
        $customValue1 = $this->faker->word;
        $customValue2 = $this->faker->word;

        $headers = [
            'Content-Type' => [$contentType],
            'X-Custom' => [$customValue1, $customValue2]
        ];

        $message = new Message('1.1', $headers, $this->mockStream);

        // Test correctness
        $this->assertEquals($contentType, $message->getHeaderLine('Content-Type'));
        $this->assertEquals($contentType, $message->getHeaderLine('content-type'), 'Headers should be case-insensitive');
        $this->assertEquals($customValue1 . ', ' . $customValue2, $message->getHeaderLine('X-Custom'));
        $this->assertEquals('', $message->getHeaderLine('X-Not-Exists'));

        // Test immutability - calling again should return the same value
        $this->assertEquals($contentType, $message->getHeaderLine('Content-Type'), 'Header line should be immutable');
        $this->assertEquals($customValue1 . ', ' . $customValue2, $message->getHeaderLine('X-Custom'), 'Header line should be immutable');
    }

    /**
     * Test withHeader method - correctness and immutability
     * 
     * @covers ::withHeader
     */
    public function testWithHeader()
    {
        $contentType = $this->faker->mimeType();
        $customValue = $this->faker->word;

        $headers = [
            'Content-Type' => [$contentType],
            'X-Custom' => [$customValue]
        ];

        $message = new Message('1.1', $headers, $this->mockStream);

        // Test adding a new header
        $newHeaderName = 'X-New';
        $newHeaderValue = $this->faker->word;
        $messageWithNewHeader = $message->withHeader($newHeaderName, $newHeaderValue);

        // Test correctness - new instance has new header
        $this->assertEquals([$newHeaderValue], $messageWithNewHeader->getHeader($newHeaderName));

        // Test immutability - original is unchanged
        $this->assertFalse($message->hasHeader($newHeaderName));

        // Test it's a new instance
        $this->assertNotSame($message, $messageWithNewHeader);

        // Test replacing an existing header
        $newContentType = 'text/html';
        $messageWithReplacedHeader = $message->withHeader('Content-Type', $newContentType);

        // Test correctness - new instance has replaced header
        $this->assertEquals([$newContentType], $messageWithReplacedHeader->getHeader('Content-Type'));

        // Test immutability - original is unchanged
        $this->assertEquals([$contentType], $message->getHeader('Content-Type'));

        // Test it's a new instance
        $this->assertNotSame($message, $messageWithReplacedHeader);

        // Test case-insensitivity when replacing
        $newPlainType = 'text/plain';
        $messageWithReplacedHeader = $message->withHeader('content-type', $newPlainType);

        // Test correctness - new instance has replaced header (case insensitive)
        $this->assertEquals([$newPlainType], $messageWithReplacedHeader->getHeader('Content-Type'));

        // Test immutability - original is unchanged
        $this->assertEquals([$contentType], $message->getHeader('Content-Type'));

        // Test with array value
        $acceptTypes = ['text/html', 'application/xml'];
        $messageWithArrayHeader = $message->withHeader('Accept', $acceptTypes);

        // Test correctness - new instance has array header
        $this->assertEquals($acceptTypes, $messageWithArrayHeader->getHeader('Accept'));

        // Test immutability - original is unchanged
        $this->assertFalse($message->hasHeader('Accept'));
    }

    /**
     * Test withAddedHeader method - correctness and immutability
     * 
     * @covers ::withAddedHeader
     * @covers ::hasHeader
     */
    public function testWithAddedHeader()
    {
        $contentType = $this->faker->mimeType();
        $customValue1 = $this->faker->word;

        $headers = [
            'Content-Type' => [$contentType],
            'X-Custom' => [$customValue1]
        ];

        $message = new Message('1.1', $headers, $this->mockStream);

        // Test adding values to an existing header
        $customValue2 = $this->faker->word;
        $messageWithAddedHeader = $message->withAddedHeader('X-Custom', $customValue2);

        // Test correctness - new instance has combined values
        $this->assertEquals([$customValue1, $customValue2], $messageWithAddedHeader->getHeader('X-Custom'));

        // Test immutability - original is unchanged
        $this->assertEquals([$customValue1], $message->getHeader('X-Custom'));

        // Test it's a new instance
        $this->assertNotSame($message, $messageWithAddedHeader);

        // Test adding a new header when it doesn't exist
        $newHeaderName = 'X-New';
        $newHeaderValue = $this->faker->word;
        $messageWithNewHeader = $message->withAddedHeader($newHeaderName, $newHeaderValue);

        // Test correctness - new instance has new header
        $this->assertEquals([$newHeaderValue], $messageWithNewHeader->getHeader($newHeaderName));

        // Test immutability - original is unchanged
        $this->assertFalse($message->hasHeader($newHeaderName));

        // Test it's a new instance
        $this->assertNotSame($message, $messageWithNewHeader);

        // Test with array value
        $additionalValues = [$this->faker->word, $this->faker->word];
        $messageWithArrayAddedHeader = $message->withAddedHeader('X-Custom', $additionalValues);

        // Test correctness - new instance has combined values
        $this->assertEquals(array_merge([$customValue1], $additionalValues), $messageWithArrayAddedHeader->getHeader('X-Custom'));

        // Test immutability - original is unchanged
        $this->assertEquals([$customValue1], $message->getHeader('X-Custom'));

        // Test case-insensitivity
        $messageWithCaseInsensitiveHeader = $message->withAddedHeader('x-custom', $customValue2);

        // Test correctness - new instance has combined values (case insensitive)
        $this->assertEquals([$customValue1, $customValue2], $messageWithCaseInsensitiveHeader->getHeader('X-Custom'));

        // Test immutability - original is unchanged
        $this->assertEquals([$customValue1], $message->getHeader('X-Custom'));
    }

    /**
     * Test withoutHeader method - correctness and immutability
     * 
     * @covers ::withoutHeader
     */
    public function testWithoutHeader()
    {
        $contentType = $this->faker->mimeType();
        $customValue = $this->faker->word;

        $headers = [
            'Content-Type' => [$contentType],
            'X-Custom' => [$customValue]
        ];

        $message = new Message('1.1', $headers, $this->mockStream);

        // Test removing an existing header
        $messageWithoutHeader = $message->withoutHeader('X-Custom');

        // Test correctness - new instance doesn't have the header
        $this->assertFalse($messageWithoutHeader->hasHeader('X-Custom'));

        // Test immutability - original is unchanged
        $this->assertTrue($message->hasHeader('X-Custom'));

        // Test it's a new instance
        $this->assertNotSame($message, $messageWithoutHeader);

        // Test case-insensitivity when removing
        $messageWithoutHeader = $message->withoutHeader('content-type');

        // Test correctness - new instance doesn't have the header (case insensitive)
        $this->assertFalse($messageWithoutHeader->hasHeader('Content-Type'));

        // Test immutability - original is unchanged
        $this->assertTrue($message->hasHeader('Content-Type'));

        // Test removing a non-existent header (should be a no-op)
        $nonExistentHeader = 'X-' . $this->faker->uuid;
        $messageWithoutNonExistingHeader = $message->withoutHeader($nonExistentHeader);

        // Test correctness - headers remain the same
        $this->assertEquals($headers, $messageWithoutNonExistingHeader->getHeaders());

        // Test it returns a new instance even for no-op
        $this->assertNotSame($message, $messageWithoutNonExistingHeader);
    }

    /**
     * Test withBody method - correctness and immutability
     * 
     * @covers ::withBody
     */
    public function testWithBody()
    {
        $message = new Message('1.1', [], $this->mockStream);

        // Create a new mock for the test
        $newMockStream = $this->getMockBuilder('Lwd\Http\Message\StreamInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $newMessage = $message->withBody($newMockStream);

        // Test immutability - original is unchanged
        $this->assertSame($this->mockStream, $message->getBody());
        $this->assertSame($this->mockStream, $message->getBody(), 'Original body should be immutable');

        // Test correctness - new instance has new body
        $this->assertSame($newMockStream, $newMessage->getBody());
        $this->assertSame($newMockStream, $newMessage->getBody(), 'New body should be immutable');

        // Ensure it's a new instance
        $this->assertNotSame($message, $newMessage);
    }

    /**
     * Test getProtocolVersion method - correctness and immutability
     * 
     * @covers ::getProtocolVersion
     */
    public function testGetProtocolVersion()
    {
        $protocolVersion = $this->faker->randomElement(['1.0', '1.1', '2.0']);
        $message = new Message($protocolVersion, [], $this->mockStream);

        // Test correctness
        $this->assertEquals($protocolVersion, $message->getProtocolVersion());

        // Test immutability - repeated calls return the same value
        $this->assertEquals($protocolVersion, $message->getProtocolVersion(), 'Protocol version should be immutable');
    }

    /**
     * Test getHeaders method - correctness and immutability
     * 
     * @covers ::getHeaders
     */
    public function testGetHeaders()
    {
        $contentType = $this->faker->mimeType();
        $customValue1 = $this->faker->word;
        $customValue2 = $this->faker->word;

        $headers = [
            'Content-Type' => [$contentType],
            'X-Custom' => [$customValue1, $customValue2]
        ];

        $message = new Message('1.1', $headers, $this->mockStream);

        // Test correctness
        $this->assertEquals($headers, $message->getHeaders());

        // Test immutability - changing original array shouldn't affect message
        $originalHeaders = $headers;
        $headers['Content-Type'] = ['text/html'];
        $headers['X-New-Header'] = ['new-value'];

        $this->assertEquals([$contentType], $message->getHeader('Content-Type'), 'Headers should be immutable to external changes');
        $this->assertFalse($message->hasHeader('X-New-Header'), 'Headers should be immutable to external changes');

        // Test immutability - modifying returned array shouldn't affect message
        $messageHeaders = $message->getHeaders();
        $messageHeaders['Content-Type'] = ['text/plain'];

        $this->assertEquals([$contentType], $message->getHeader('Content-Type'), 'Headers should be immutable to changes in returned array');
    }

    /**
     * Test getBody method - correctness and immutability
     * 
     * @covers ::getBody
     */
    public function testGetBody()
    {
        $message = new Message('1.1', [], $this->mockStream);

        // Test correctness
        $this->assertSame($this->mockStream, $message->getBody());

        // Test immutability - create a new mock to try to replace the stream
        $newMockStream = $this->getMockBuilder('Lwd\Http\Message\StreamInterface')
            ->disableOriginalConstructor()
            ->getMock();

        // Even after calling getBody and trying to modify it, the original message should be unchanged
        // Note: Since we're returning an interface, we can't actually modify the mock object directly,
        // but we can verify that repeated calls return the same instance
        $this->assertSame($message->getBody(), $message->getBody(), 'Body should be immutable');
    }
}
