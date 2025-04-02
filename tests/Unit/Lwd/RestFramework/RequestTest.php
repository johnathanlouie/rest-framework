<?php

namespace Tests\Unit\Lwd\RestFramework;

use Lwd\RestFramework\Request;
use Lwd\Http\Message\UriInterface;
use Lwd\Http\Message\StreamInterface;
use PHPUnit_Framework_TestCase;

/**
 * Tests for the Request class
 * 
 * @coversDefaultClass \Lwd\RestFramework\Request
 */
class RequestTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var UriInterface
     */
    protected $mockUri;

    /**
     * @var StreamInterface
     */
    protected $mockStream;

    protected function setUp()
    {
        // Create mocks for UriInterface and StreamInterface for testing
        $this->mockUri = $this->getMock('Lwd\Http\Message\UriInterface');
        $this->mockStream = $this->getMock('Lwd\Http\Message\StreamInterface');
    }

    /**
     * Test constructor and accessor methods
     * 
     * @covers ::__construct
     * @covers ::getMethod
     * @covers ::getUri
     */
    public function testConstructor()
    {
        $method = 'GET';
        $protocolVersion = '1.1';
        $headers = [
            'Content-Type' => ['application/json'],
            'Accept' => ['application/json']
        ];

        $request = new Request($method, $this->mockUri, $protocolVersion, $headers, $this->mockStream);

        $this->assertEquals($method, $request->getMethod());
        $this->assertEquals($method, $request->getMethod(), 'Method should be immutable'); // Test getter immutability

        $this->assertSame($this->mockUri, $request->getUri());
        $this->assertSame($this->mockUri, $request->getUri(), 'URI should be immutable'); // Test getter immutability

        $this->assertEquals($protocolVersion, $request->getProtocolVersion());
        $this->assertEquals($protocolVersion, $request->getProtocolVersion(), 'Protocol version should be immutable'); // Test getter immutability

        $this->assertEquals($headers, $request->getHeaders());
        $this->assertEquals($headers, $request->getHeaders(), 'Headers should be immutable'); // Test getter immutability

        $this->assertSame($this->mockStream, $request->getBody());
        $this->assertSame($this->mockStream, $request->getBody(), 'Body should be immutable'); // Test getter immutability
    }

    /**
     * Test withMethod method - correctness and immutability
     * 
     * @covers ::withMethod
     */
    public function testWithMethod()
    {
        $request = new Request('GET', $this->mockUri, '1.1', [], $this->mockStream);
        $newRequest = $request->withMethod('POST');

        // Test immutability - original is unchanged
        $this->assertEquals('GET', $request->getMethod());

        // Test correctness - new instance has new method
        $this->assertEquals('POST', $newRequest->getMethod());

        // Ensure it's a new instance
        $this->assertNotSame($request, $newRequest);
    }

    /**
     * Test getMethod method - immutability
     * 
     * @covers ::getMethod
     */
    public function testGetMethod()
    {
        $method = 'GET';
        $request = new Request($method, $this->mockUri, '1.1', [], $this->mockStream);

        // Call method multiple times to verify it returns the same value
        $this->assertEquals($method, $request->getMethod());
        $this->assertEquals($method, $request->getMethod(), 'Method should be immutable');
    }

    /**
     * Test getRequestTarget method with explicit request target
     * 
     * @covers ::getRequestTarget
     */
    public function testGetRequestTargetWithExplicitTarget()
    {
        $requestTarget = '/explicit-target?query=value';
        $request = new Request('GET', $this->mockUri, '1.1', [], $this->mockStream, $requestTarget);

        $this->assertEquals($requestTarget, $request->getRequestTarget());
        // Test immutability - calling it again should return the same value
        $this->assertEquals($requestTarget, $request->getRequestTarget(), 'Request target should be immutable');
    }

    /**
     * Test getRequestTarget method derived from URI
     * 
     * @covers ::getRequestTarget
     */
    public function testGetRequestTargetDerivedFromUri()
    {
        // Set up URI mock to return a path and query
        $this->mockUri->expects($this->any())
            ->method('getPath')
            ->will($this->returnValue('/test-path'));

        $this->mockUri->expects($this->any())
            ->method('getQuery')
            ->will($this->returnValue('query=value'));

        $request = new Request('GET', $this->mockUri, '1.1', [], $this->mockStream);
        $expectedTarget = '/test-path?query=value';

        $this->assertEquals($expectedTarget, $request->getRequestTarget());
        // Test immutability - calling it again should return the same value
        $this->assertEquals($expectedTarget, $request->getRequestTarget(), 'Request target should be immutable');
    }

    /**
     * Test getRequestTarget method with empty path
     * 
     * @covers ::getRequestTarget
     */
    public function testGetRequestTargetWithEmptyPath()
    {
        // Set up URI mock to return an empty path
        $this->mockUri->expects($this->any())
            ->method('getPath')
            ->will($this->returnValue(''));

        $this->mockUri->expects($this->any())
            ->method('getQuery')
            ->will($this->returnValue(''));

        $request = new Request('GET', $this->mockUri, '1.1', [], $this->mockStream);
        $expectedTarget = '/';

        $this->assertEquals($expectedTarget, $request->getRequestTarget());
        // Test immutability - calling it again should return the same value
        $this->assertEquals($expectedTarget, $request->getRequestTarget(), 'Request target should be immutable');
    }

    /**
     * Test getRequestTarget method with path and no query
     * 
     * @covers ::getRequestTarget
     */
    public function testGetRequestTargetWithPathNoQuery()
    {
        // Set up URI mock to return a path but no query
        $this->mockUri->expects($this->any())
            ->method('getPath')
            ->will($this->returnValue('/test-path'));

        $this->mockUri->expects($this->any())
            ->method('getQuery')
            ->will($this->returnValue(''));

        $request = new Request('GET', $this->mockUri, '1.1', [], $this->mockStream);
        $expectedTarget = '/test-path';

        $this->assertEquals($expectedTarget, $request->getRequestTarget());
        // Test immutability - calling it again should return the same value
        $this->assertEquals($expectedTarget, $request->getRequestTarget(), 'Request target should be immutable');
    }

    /**
     * Test withRequestTarget method - correctness and immutability
     * 
     * @covers ::withRequestTarget
     */
    public function testWithRequestTarget()
    {
        // Set up URI mock with default values for getPath and getQuery
        $this->mockUri->expects($this->any())
            ->method('getPath')
            ->will($this->returnValue('/default-path'));

        $this->mockUri->expects($this->any())
            ->method('getQuery')
            ->will($this->returnValue(''));

        $request = new Request('GET', $this->mockUri, '1.1', [], $this->mockStream);
        $customTarget = '/custom-target';
        $newRequest = $request->withRequestTarget($customTarget);

        // Test immutability - original uses URI-derived target
        $this->assertEquals('/default-path', $request->getRequestTarget());
        $this->assertEquals('/default-path', $request->getRequestTarget(), 'Original request target should be immutable');

        // Test correctness - new instance has custom target
        $this->assertEquals($customTarget, $newRequest->getRequestTarget());
        $this->assertEquals($customTarget, $newRequest->getRequestTarget(), 'New request target should be immutable');

        // Ensure it's a new instance
        $this->assertNotSame($request, $newRequest);
    }

    /**
     * Test getUri method - immutability
     * 
     * @covers ::getUri
     */
    public function testGetUri()
    {
        $request = new Request('GET', $this->mockUri, '1.1', [], $this->mockStream);

        // Call method multiple times to verify it returns the same value
        $this->assertSame($this->mockUri, $request->getUri());
        $this->assertSame($this->mockUri, $request->getUri(), 'URI should be immutable');
    }

    /**
     * Test withUri method - correctness and immutability
     * 
     * @covers ::withUri
     */
    public function testWithUri()
    {
        $request = new Request('GET', $this->mockUri, '1.1', [], $this->mockStream);

        // Create a new mock URI
        $newMockUri = $this->getMock('Lwd\Http\Message\UriInterface');

        $newRequest = $request->withUri($newMockUri);

        // Test immutability - original is unchanged
        $this->assertSame($this->mockUri, $request->getUri());
        $this->assertSame($this->mockUri, $request->getUri(), 'Original URI should be immutable');

        // Test correctness - new instance has new URI
        $this->assertSame($newMockUri, $newRequest->getUri());
        $this->assertSame($newMockUri, $newRequest->getUri(), 'New URI should be immutable');

        // Ensure it's a new instance
        $this->assertNotSame($request, $newRequest);
    }

    /**
     * Test withUri method with Host header handling - not preserving host
     * 
     * @covers ::withUri
     */
    public function testWithUriUpdatesHostHeader()
    {
        $request = new Request('GET', $this->mockUri, '1.1', [], $this->mockStream);

        // Create a new mock URI that returns a host
        $newMockUri = $this->getMock('Lwd\Http\Message\UriInterface');
        $newMockUri->expects($this->any())
            ->method('getHost')
            ->will($this->returnValue('example.com'));

        $newMockUri->expects($this->any())
            ->method('getPort')
            ->will($this->returnValue(null));

        $newRequest = $request->withUri($newMockUri);

        // Test immutability - original is unchanged
        $this->assertFalse($request->hasHeader('Host'));

        // Test that the Host header was set based on the URI's host
        $this->assertEquals(['example.com'], $newRequest->getHeader('Host'));
        $this->assertEquals(['example.com'], $newRequest->getHeader('Host'), 'Host header should be immutable');
    }

    /**
     * Test withUri method with Host header handling - including port
     * 
     * @covers ::withUri
     */
    public function testWithUriUpdatesHostHeaderWithPort()
    {
        $request = new Request('GET', $this->mockUri, '1.1', [], $this->mockStream);

        // Create a new mock URI that returns a host and port
        $newMockUri = $this->getMock('Lwd\Http\Message\UriInterface');
        $newMockUri->expects($this->any())
            ->method('getHost')
            ->will($this->returnValue('example.com'));

        $newMockUri->expects($this->any())
            ->method('getPort')
            ->will($this->returnValue(8080));

        $newRequest = $request->withUri($newMockUri);

        // Test immutability - original is unchanged
        $this->assertFalse($request->hasHeader('Host'));

        // Test that the Host header was set based on the URI's host and port
        $this->assertEquals(['example.com:8080'], $newRequest->getHeader('Host'));
        $this->assertEquals(['example.com:8080'], $newRequest->getHeader('Host'), 'Host header should be immutable');
    }

    /**
     * Test withUri method with preserveHost flag
     * 
     * @covers ::withUri
     */
    public function testWithUriPreservesHost()
    {
        $headers = [
            'Host' => ['original.example.com']
        ];

        $request = new Request('GET', $this->mockUri, '1.1', $headers, $this->mockStream);

        // Create a new mock URI that returns a host
        $newMockUri = $this->getMock('Lwd\Http\Message\UriInterface');
        $newMockUri->expects($this->any())
            ->method('getHost')
            ->will($this->returnValue('new.example.com'));

        // Use the preserveHost flag
        $newRequest = $request->withUri($newMockUri, true);

        // Test immutability - original is unchanged
        $this->assertEquals(['original.example.com'], $request->getHeader('Host'));
        $this->assertEquals(['original.example.com'], $request->getHeader('Host'), 'Original Host header should be immutable');

        // Test that the original Host header was preserved
        $this->assertEquals(['original.example.com'], $newRequest->getHeader('Host'));
        $this->assertEquals(['original.example.com'], $newRequest->getHeader('Host'), 'Preserved Host header should be immutable');
    }

    /**
     * Test withUri method with empty host in URI
     * 
     * @covers ::withUri
     */
    public function testWithUriEmptyHost()
    {
        $request = new Request('GET', $this->mockUri, '1.1', [], $this->mockStream);

        // Create a new mock URI that returns an empty host
        $newMockUri = $this->getMock('Lwd\Http\Message\UriInterface');
        $newMockUri->expects($this->any())
            ->method('getHost')
            ->will($this->returnValue(''));

        $newRequest = $request->withUri($newMockUri);

        // Test immutability - original is unchanged
        $this->assertFalse($request->hasHeader('Host'));

        // Test that no Host header was added
        $this->assertFalse($newRequest->hasHeader('Host'));
    }
}
