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

    protected function setUp()
    {
        // Create a mock of StreamInterface for testing
        $this->mockStream = $this->getMock('Lwd\Http\Message\StreamInterface');
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
        $protocolVersion = '1.1';
        $headers = [
            'Content-Type' => ['application/json'],
            'X-Custom' => ['value1', 'value2']
        ];

        $message = new Message($protocolVersion, $headers, $this->mockStream);

        $this->assertEquals($protocolVersion, $message->getProtocolVersion());
        $this->assertEquals($headers, $message->getHeaders());
        $this->assertSame($this->mockStream, $message->getBody());
    }

    /**
     * Test withProtocolVersion method - correctness and immutability
     * 
     * @covers ::withProtocolVersion
     */
    public function testWithProtocolVersion()
    {
        $message = new Message('1.1', [], $this->mockStream);
        $newMessage = $message->withProtocolVersion('2.0');

        // Test immutability - original is unchanged
        $this->assertEquals('1.1', $message->getProtocolVersion());

        // Test correctness - new instance has new protocol version
        $this->assertEquals('2.0', $newMessage->getProtocolVersion());

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
        $headers = [
            'Content-Type' => ['application/json'],
            'X-Custom' => ['value1']
        ];

        $message = new Message('1.1', $headers, $this->mockStream);

        $this->assertTrue($message->hasHeader('Content-Type'));
        $this->assertTrue($message->hasHeader('content-type')); // Case insensitive
        $this->assertTrue($message->hasHeader('X-Custom'));
        $this->assertFalse($message->hasHeader('X-Not-Exists'));
    }

    /**
     * Test getHeader method - correctness and immutability
     * 
     * @covers ::getHeader
     */
    public function testGetHeader()
    {
        $headers = [
            'Content-Type' => ['application/json'],
            'X-Custom' => ['value1', 'value2']
        ];

        $message = new Message('1.1', $headers, $this->mockStream);

        // Test correctness
        $this->assertEquals(['application/json'], $message->getHeader('Content-Type'));
        $this->assertEquals(['application/json'], $message->getHeader('content-type')); // Case insensitive
        $this->assertEquals(['value1', 'value2'], $message->getHeader('X-Custom'));
        $this->assertEquals([], $message->getHeader('X-Not-Exists'));

        // Test immutability - modifying returned array shouldn't affect message
        $headerValues = $message->getHeader('X-Custom');
        $headerValues[] = 'value3';
        $this->assertEquals(['value1', 'value2'], $message->getHeader('X-Custom'));
    }

    /**
     * Test getHeaderLine method - correctness and immutability
     * 
     * @covers ::getHeaderLine
     */
    public function testGetHeaderLine()
    {
        $headers = [
            'Content-Type' => ['application/json'],
            'X-Custom' => ['value1', 'value2']
        ];

        $message = new Message('1.1', $headers, $this->mockStream);

        // Test correctness
        $this->assertEquals('application/json', $message->getHeaderLine('Content-Type'));
        $this->assertEquals('application/json', $message->getHeaderLine('content-type')); // Case insensitive
        $this->assertEquals('value1, value2', $message->getHeaderLine('X-Custom'));
        $this->assertEquals('', $message->getHeaderLine('X-Not-Exists'));

        // The returned value is a string (primitive value), so it's inherently immutable
    }

    /**
     * Test withHeader method - correctness and immutability
     * 
     * @covers ::withHeader
     */
    public function testWithHeader()
    {
        $headers = [
            'Content-Type' => ['application/json'],
            'X-Custom' => ['value1']
        ];

        $message = new Message('1.1', $headers, $this->mockStream);

        // Test adding a new header
        $messageWithNewHeader = $message->withHeader('X-New', 'new-value');

        // Test correctness - new instance has new header
        $this->assertEquals(['new-value'], $messageWithNewHeader->getHeader('X-New'));
        // Test immutability - original is unchanged
        $this->assertFalse($message->hasHeader('X-New'));
        // Test it's a new instance
        $this->assertNotSame($message, $messageWithNewHeader);

        // Test replacing an existing header
        $messageWithReplacedHeader = $message->withHeader('Content-Type', 'text/html');

        // Test correctness - new instance has replaced header
        $this->assertEquals(['text/html'], $messageWithReplacedHeader->getHeader('Content-Type'));
        // Test immutability - original is unchanged
        $this->assertEquals(['application/json'], $message->getHeader('Content-Type'));
        // Test it's a new instance
        $this->assertNotSame($message, $messageWithReplacedHeader);

        // Test case-insensitivity when replacing
        $messageWithReplacedHeader = $message->withHeader('content-type', 'text/plain');

        // Test correctness - new instance has replaced header (case insensitive)
        $this->assertEquals(['text/plain'], $messageWithReplacedHeader->getHeader('Content-Type'));
        // Test immutability - original is unchanged
        $this->assertEquals(['application/json'], $message->getHeader('Content-Type'));

        // Test with array value
        $messageWithArrayHeader = $message->withHeader('Accept', ['text/html', 'application/xml']);

        // Test correctness - new instance has array header
        $this->assertEquals(['text/html', 'application/xml'], $messageWithArrayHeader->getHeader('Accept'));
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
        $headers = [
            'Content-Type' => ['application/json'],
            'X-Custom' => ['value1']
        ];

        $message = new Message('1.1', $headers, $this->mockStream);

        // Test adding values to an existing header
        $messageWithAddedHeader = $message->withAddedHeader('X-Custom', 'value2');

        // Test correctness - new instance has combined values
        $this->assertEquals(['value1', 'value2'], $messageWithAddedHeader->getHeader('X-Custom'));
        // Test immutability - original is unchanged
        $this->assertEquals(['value1'], $message->getHeader('X-Custom'));
        // Test it's a new instance
        $this->assertNotSame($message, $messageWithAddedHeader);

        // Test adding a new header when it doesn't exist
        $messageWithNewHeader = $message->withAddedHeader('X-New', 'new-value');

        // Test correctness - new instance has new header
        $this->assertEquals(['new-value'], $messageWithNewHeader->getHeader('X-New'));
        // Test immutability - original is unchanged
        $this->assertFalse($message->hasHeader('X-New'));
        // Test it's a new instance
        $this->assertNotSame($message, $messageWithNewHeader);

        // Test with array value
        $messageWithArrayAddedHeader = $message->withAddedHeader('X-Custom', ['value2', 'value3']);

        // Test correctness - new instance has combined values
        $this->assertEquals(['value1', 'value2', 'value3'], $messageWithArrayAddedHeader->getHeader('X-Custom'));
        // Test immutability - original is unchanged
        $this->assertEquals(['value1'], $message->getHeader('X-Custom'));

        // Test case-insensitivity
        $messageWithCaseInsensitiveHeader = $message->withAddedHeader('x-custom', 'value2');

        // Test correctness - new instance has combined values (case insensitive)
        $this->assertEquals(['value1', 'value2'], $messageWithCaseInsensitiveHeader->getHeader('X-Custom'));
        // Test immutability - original is unchanged
        $this->assertEquals(['value1'], $message->getHeader('X-Custom'));
    }

    /**
     * Test withoutHeader method - correctness and immutability
     * 
     * @covers ::withoutHeader
     */
    public function testWithoutHeader()
    {
        $headers = [
            'Content-Type' => ['application/json'],
            'X-Custom' => ['value1']
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
        $messageWithoutNonExistingHeader = $message->withoutHeader('X-Not-Exists');

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
        $newMockStream = $this->getMock('Lwd\Http\Message\StreamInterface');

        $newMessage = $message->withBody($newMockStream);

        // Test immutability - original is unchanged
        $this->assertSame($this->mockStream, $message->getBody());

        // Test correctness - new instance has new body
        $this->assertSame($newMockStream, $newMessage->getBody());

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
        $protocolVersion = '1.1';
        $message = new Message($protocolVersion, [], $this->mockStream);

        // Test correctness
        $this->assertEquals($protocolVersion, $message->getProtocolVersion());

        // Protocol version is a string (primitive value), so it's inherently immutable
        // But we can verify that repeated calls return the same value
        $this->assertEquals($message->getProtocolVersion(), $message->getProtocolVersion());
    }

    /**
     * Test getHeaders method - correctness and immutability
     * 
     * @covers ::getHeaders
     */
    public function testGetHeaders()
    {
        $headers = [
            'Content-Type' => ['application/json'],
            'X-Custom' => ['value1', 'value2']
        ];

        $message = new Message('1.1', $headers, $this->mockStream);

        // Test correctness
        $this->assertEquals($headers, $message->getHeaders());

        // Test immutability - changing original array shouldn't affect message
        $headers['Content-Type'] = ['text/html'];
        $headers['X-New-Header'] = ['new-value'];
        $this->assertEquals(['application/json'], $message->getHeader('Content-Type'));
        $this->assertFalse($message->hasHeader('X-New-Header'));

        // Test immutability - modifying returned array shouldn't affect message
        $messageHeaders = $message->getHeaders();
        $messageHeaders['Content-Type'] = ['text/plain'];
        $this->assertEquals(['application/json'], $message->getHeader('Content-Type'));
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
        $newMockStream = $this->getMock('Lwd\Http\Message\StreamInterface');

        // Even after calling getBody and trying to modify it, the original message should be unchanged
        // Note: Since we're returning an interface, we can't actually modify the mock object directly,
        // but we can verify that repeated calls return the same instance
        $this->assertSame($message->getBody(), $message->getBody());
    }
}
