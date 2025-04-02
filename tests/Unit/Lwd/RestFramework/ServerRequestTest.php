<?php

namespace Tests\Unit\Lwd\RestFramework;

use Lwd\RestFramework\ServerRequest;
use Lwd\Http\Message\UriInterface;
use Lwd\Http\Message\StreamInterface;
use PHPUnit_Framework_TestCase;

/**
 * Tests for the ServerRequest class
 * 
 * @coversDefaultClass \Lwd\RestFramework\ServerRequest
 */
class ServerRequestTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var UriInterface
     */
    protected $mockUri;

    /**
     * @var StreamInterface
     */
    protected $mockStream;

    /**
     * @var array
     */
    protected $serverParams;

    /**
     * @var array
     */
    protected $cookies;

    /**
     * @var array
     */
    protected $queryParams;

    /**
     * @var array
     */
    protected $uploadedFiles;

    /**
     * @var array
     */
    protected $parsedBody;

    /**
     * @var array
     */
    protected $attributes;

    protected function setUp()
    {
        // Create mocks for UriInterface and StreamInterface for testing
        $this->mockUri = $this->getMock('Lwd\Http\Message\UriInterface');
        $this->mockStream = $this->getMock('Lwd\Http\Message\StreamInterface');

        // Setup test data
        $this->serverParams = [
            'SERVER_NAME' => 'example.com',
            'REQUEST_METHOD' => 'POST',
            'REMOTE_ADDR' => '127.0.0.1'
        ];

        $this->cookies = [
            'session_id' => 'abc123',
            'user_pref' => 'dark_mode'
        ];

        $this->queryParams = [
            'page' => '1',
            'sort' => 'desc'
        ];

        $this->uploadedFiles = [
            'file1' => $this->getMock('Lwd\Http\Message\UploadedFileInterface'),
            'file2' => $this->getMock('Lwd\Http\Message\UploadedFileInterface')
        ];

        $this->parsedBody = [
            'username' => 'testuser',
            'email' => 'test@example.com'
        ];

        $this->attributes = [
            'route' => 'user.profile',
            'user_id' => 42
        ];
    }

    /**
     * Test constructor and accessor methods
     * 
     * @covers ::__construct
     * @covers ::getServerParams
     * @covers ::getCookieParams
     * @covers ::getQueryParams
     * @covers ::getUploadedFiles
     * @covers ::getParsedBody
     * @covers ::getAttributes
     */
    public function testConstructor()
    {
        $method = 'POST';
        $protocolVersion = '1.1';
        $headers = [
            'Content-Type' => ['application/json'],
            'Accept' => ['application/json']
        ];
        $requestTarget = null;

        $request = new ServerRequest(
            $method,
            $this->mockUri,
            $protocolVersion,
            $headers,
            $this->mockStream,
            $requestTarget,
            $this->serverParams,
            $this->cookies,
            $this->queryParams,
            $this->parsedBody,
            $this->uploadedFiles,
            $this->attributes
        );

        // Test that parent (Request) properties are set correctly
        $this->assertEquals($method, $request->getMethod());
        $this->assertEquals($method, $request->getMethod(), 'Method should be immutable');

        $this->assertSame($this->mockUri, $request->getUri());
        $this->assertSame($this->mockUri, $request->getUri(), 'URI should be immutable');

        $this->assertEquals($protocolVersion, $request->getProtocolVersion());
        $this->assertEquals($protocolVersion, $request->getProtocolVersion(), 'Protocol version should be immutable');

        $this->assertEquals($headers, $request->getHeaders());
        $this->assertEquals($headers, $request->getHeaders(), 'Headers should be immutable');

        $this->assertSame($this->mockStream, $request->getBody());
        $this->assertSame($this->mockStream, $request->getBody(), 'Body should be immutable');

        // Test ServerRequest specific properties
        $this->assertEquals($this->serverParams, $request->getServerParams());
        $this->assertEquals($this->serverParams, $request->getServerParams(), 'Server params should be immutable');

        $this->assertEquals($this->cookies, $request->getCookieParams());
        $this->assertEquals($this->cookies, $request->getCookieParams(), 'Cookie params should be immutable');

        $this->assertEquals($this->queryParams, $request->getQueryParams());
        $this->assertEquals($this->queryParams, $request->getQueryParams(), 'Query params should be immutable');

        $this->assertEquals($this->uploadedFiles, $request->getUploadedFiles());
        $this->assertEquals($this->uploadedFiles, $request->getUploadedFiles(), 'Uploaded files should be immutable');

        $this->assertEquals($this->parsedBody, $request->getParsedBody());
        $this->assertEquals($this->parsedBody, $request->getParsedBody(), 'Parsed body should be immutable');

        $this->assertEquals($this->attributes, $request->getAttributes());
        $this->assertEquals($this->attributes, $request->getAttributes(), 'Attributes should be immutable');
    }

    /**
     * Test getServerParams method - immutability
     * 
     * @covers ::getServerParams
     */
    public function testGetServerParams()
    {
        $request = $this->createServerRequest();

        // Call method multiple times to verify it returns the same value
        $this->assertEquals($this->serverParams, $request->getServerParams());
        $this->assertEquals($this->serverParams, $request->getServerParams(), 'Server params should be immutable');
    }

    /**
     * Test getCookieParams method - immutability
     * 
     * @covers ::getCookieParams
     */
    public function testGetCookieParams()
    {
        $request = $this->createServerRequest();

        // Call method multiple times to verify it returns the same value
        $this->assertEquals($this->cookies, $request->getCookieParams());
        $this->assertEquals($this->cookies, $request->getCookieParams(), 'Cookie params should be immutable');
    }

    /**
     * Test withCookieParams method - correctness and immutability
     * 
     * @covers ::withCookieParams
     */
    public function testWithCookieParams()
    {
        $request = $this->createServerRequest();
        $newCookies = [
            'theme' => 'light',
            'language' => 'en'
        ];

        $newRequest = $request->withCookieParams($newCookies);

        // Test immutability - original is unchanged
        $this->assertEquals($this->cookies, $request->getCookieParams());
        $this->assertEquals($this->cookies, $request->getCookieParams(), 'Original cookies should be immutable');

        // Test correctness - new instance has merged cookies
        $expectedCookies = array_merge($this->cookies, $newCookies);
        $this->assertEquals($expectedCookies, $newRequest->getCookieParams());
        $this->assertEquals($expectedCookies, $newRequest->getCookieParams(), 'New cookies should be immutable');

        // Ensure it's a new instance
        $this->assertNotSame($request, $newRequest);
    }

    /**
     * Test getQueryParams method - immutability
     * 
     * @covers ::getQueryParams
     */
    public function testGetQueryParams()
    {
        $request = $this->createServerRequest();

        // Call method multiple times to verify it returns the same value
        $this->assertEquals($this->queryParams, $request->getQueryParams());
        $this->assertEquals($this->queryParams, $request->getQueryParams(), 'Query params should be immutable');
    }

    /**
     * Test withQueryParams method - correctness and immutability
     * 
     * @covers ::withQueryParams
     */
    public function testWithQueryParams()
    {
        $request = $this->createServerRequest();
        $newQueryParams = [
            'filter' => 'active',
            'limit' => '50'
        ];

        $newRequest = $request->withQueryParams($newQueryParams);

        // Test immutability - original is unchanged
        $this->assertEquals($this->queryParams, $request->getQueryParams());
        $this->assertEquals($this->queryParams, $request->getQueryParams(), 'Original query params should be immutable');

        // Test correctness - new instance has new query params
        $this->assertEquals($newQueryParams, $newRequest->getQueryParams());
        $this->assertEquals($newQueryParams, $newRequest->getQueryParams(), 'New query params should be immutable');

        // Ensure it's a new instance
        $this->assertNotSame($request, $newRequest);
    }

    /**
     * Test getUploadedFiles method - immutability
     * 
     * @covers ::getUploadedFiles
     */
    public function testGetUploadedFiles()
    {
        $request = $this->createServerRequest();

        // Call method multiple times to verify it returns the same value
        $this->assertEquals($this->uploadedFiles, $request->getUploadedFiles());
        $this->assertEquals($this->uploadedFiles, $request->getUploadedFiles(), 'Uploaded files should be immutable');
    }

    /**
     * Test withUploadedFiles method - correctness and immutability
     * 
     * @covers ::withUploadedFiles
     */
    public function testWithUploadedFiles()
    {
        $request = $this->createServerRequest();
        $newUploadedFile = $this->getMock('Lwd\Http\Message\UploadedFileInterface');
        $newUploadedFiles = [
            'profile_pic' => $newUploadedFile
        ];

        $newRequest = $request->withUploadedFiles($newUploadedFiles);

        // Test immutability - original is unchanged
        $this->assertEquals($this->uploadedFiles, $request->getUploadedFiles());
        $this->assertEquals($this->uploadedFiles, $request->getUploadedFiles(), 'Original uploaded files should be immutable');

        // Test correctness - new instance has new uploaded files
        $this->assertEquals($newUploadedFiles, $newRequest->getUploadedFiles());
        $this->assertEquals($newUploadedFiles, $newRequest->getUploadedFiles(), 'New uploaded files should be immutable');

        // Ensure it's a new instance
        $this->assertNotSame($request, $newRequest);
    }

    /**
     * Test getParsedBody method - immutability
     * 
     * @covers ::getParsedBody
     */
    public function testGetParsedBody()
    {
        $request = $this->createServerRequest();

        // Call method multiple times to verify it returns the same value
        $this->assertEquals($this->parsedBody, $request->getParsedBody());
        $this->assertEquals($this->parsedBody, $request->getParsedBody(), 'Parsed body should be immutable');
    }

    /**
     * Test withParsedBody method - correctness and immutability
     * 
     * @covers ::withParsedBody
     */
    public function testWithParsedBody()
    {
        $request = $this->createServerRequest();
        $newParsedBody = [
            'name' => 'John Doe',
            'age' => 30
        ];

        $newRequest = $request->withParsedBody($newParsedBody);

        // Test immutability - original is unchanged
        $this->assertEquals($this->parsedBody, $request->getParsedBody());
        $this->assertEquals($this->parsedBody, $request->getParsedBody(), 'Original parsed body should be immutable');

        // Test correctness - new instance has new parsed body
        $this->assertEquals($newParsedBody, $newRequest->getParsedBody());
        $this->assertEquals($newParsedBody, $newRequest->getParsedBody(), 'New parsed body should be immutable');

        // Ensure it's a new instance
        $this->assertNotSame($request, $newRequest);
    }

    /**
     * Test withParsedBody method with object body
     * 
     * @covers ::withParsedBody
     */
    public function testWithParsedBodyObject()
    {
        $request = $this->createServerRequest();
        $newParsedBody = new \stdClass();
        $newParsedBody->name = 'John Doe';
        $newParsedBody->age = 30;

        $newRequest = $request->withParsedBody($newParsedBody);

        // Test immutability - original is unchanged
        $this->assertEquals($this->parsedBody, $request->getParsedBody());
        $this->assertEquals($this->parsedBody, $request->getParsedBody(), 'Original parsed body should be immutable');

        // Test correctness - new instance has new parsed body
        $this->assertEquals($newParsedBody, $newRequest->getParsedBody());
        $this->assertEquals($newParsedBody, $newRequest->getParsedBody(), 'New parsed body should be immutable');
    }

    /**
     * Test withParsedBody method with null body
     * 
     * @covers ::withParsedBody
     */
    public function testWithParsedBodyNull()
    {
        $request = $this->createServerRequest();
        $newRequest = $request->withParsedBody(null);

        // Test immutability - original is unchanged
        $this->assertEquals($this->parsedBody, $request->getParsedBody());
        $this->assertEquals($this->parsedBody, $request->getParsedBody(), 'Original parsed body should be immutable');

        // Test correctness - new instance has null parsed body
        $this->assertNull($newRequest->getParsedBody());
        $this->assertNull($newRequest->getParsedBody(), 'New null parsed body should be immutable');
    }

    /**
     * Test getAttributes method - immutability
     * 
     * @covers ::getAttributes
     */
    public function testGetAttributes()
    {
        $request = $this->createServerRequest();

        // Call method multiple times to verify it returns the same value
        $this->assertEquals($this->attributes, $request->getAttributes());
        $this->assertEquals($this->attributes, $request->getAttributes(), 'Attributes should be immutable');
    }

    /**
     * Test getAttribute method
     * 
     * @covers ::getAttribute
     */
    public function testGetAttribute()
    {
        $request = $this->createServerRequest();

        // Test getting existing attribute
        $this->assertEquals(42, $request->getAttribute('user_id'));
        $this->assertEquals(42, $request->getAttribute('user_id'), 'Attribute should be immutable');

        // Test getting non-existent attribute with default
        $this->assertEquals('default', $request->getAttribute('non_existent', 'default'));
        $this->assertEquals('default', $request->getAttribute('non_existent', 'default'), 'Default should be consistently returned');

        // Test getting non-existent attribute without default
        $this->assertNull($request->getAttribute('non_existent'));
        $this->assertNull($request->getAttribute('non_existent'), 'Null should be consistently returned for non-existent attributes');
    }

    /**
     * Test withAttribute method - correctness and immutability
     * 
     * @covers ::withAttribute
     */
    public function testWithAttribute()
    {
        $request = $this->createServerRequest();
        $newRequest = $request->withAttribute('role', 'admin');

        // Test immutability - original is unchanged
        $this->assertFalse(isset($request->getAttributes()['role']));
        $this->assertEquals($this->attributes, $request->getAttributes(), 'Original attributes should be immutable');

        // Test correctness - new instance has new attribute
        $this->assertEquals('admin', $newRequest->getAttribute('role'));
        $this->assertEquals('admin', $newRequest->getAttribute('role'), 'New attribute should be immutable');

        // Original attributes should still be present in new instance
        $this->assertEquals(42, $newRequest->getAttribute('user_id'));
        $this->assertEquals(42, $newRequest->getAttribute('user_id'), 'Existing attribute should be immutable');

        // Ensure it's a new instance
        $this->assertNotSame($request, $newRequest);
    }

    /**
     * Test withoutAttribute method - correctness and immutability
     * 
     * @covers ::withoutAttribute
     */
    public function testWithoutAttribute()
    {
        $request = $this->createServerRequest();
        $newRequest = $request->withoutAttribute('user_id');

        // Test immutability - original is unchanged
        $this->assertEquals(42, $request->getAttribute('user_id'));
        $this->assertEquals(42, $request->getAttribute('user_id'), 'Original attribute should be immutable');
        $this->assertEquals($this->attributes, $request->getAttributes(), 'Original attributes should be immutable');

        // Test correctness - new instance has attribute removed
        $this->assertNull($newRequest->getAttribute('user_id'));
        $this->assertNull($newRequest->getAttribute('user_id'), 'Removed attribute should consistently return null');
        $this->assertFalse(isset($newRequest->getAttributes()['user_id']));

        // Other attributes should still be present
        $this->assertEquals('user.profile', $newRequest->getAttribute('route'));
        $this->assertEquals('user.profile', $newRequest->getAttribute('route'), 'Remaining attribute should be immutable');

        // Ensure it's a new instance
        $this->assertNotSame($request, $newRequest);
    }

    /**
     * Test withoutAttribute method with non-existent attribute
     * 
     * @covers ::withoutAttribute
     */
    public function testWithoutAttributeNonExistent()
    {
        $request = $this->createServerRequest();
        $newRequest = $request->withoutAttribute('non_existent');

        // Test immutability - original is unchanged
        $this->assertEquals($this->attributes, $request->getAttributes());
        $this->assertEquals($this->attributes, $request->getAttributes(), 'Original attributes should be immutable');

        // Test that removing a non-existent attribute doesn't affect other attributes
        $this->assertEquals($request->getAttributes(), $newRequest->getAttributes());
        $this->assertEquals($request->getAttributes(), $newRequest->getAttributes(), 'Attributes should be immutable when removing non-existent attribute');

        // But it should still be a new instance
        $this->assertNotSame($request, $newRequest);
    }

    /**
     * Helper method to create a ServerRequest instance for testing
     *
     * @return ServerRequest
     */
    private function createServerRequest()
    {
        return new ServerRequest(
            'POST',
            $this->mockUri,
            '1.1',
            ['Content-Type' => ['application/json']],
            $this->mockStream,
            null,
            $this->serverParams,
            $this->cookies,
            $this->queryParams,
            $this->parsedBody,
            $this->uploadedFiles,
            $this->attributes
        );
    }
}
