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

    /**
     * @var \Faker\Generator
     */
    protected $faker;

    protected function setUp()
    {
        // Initialize Faker
        $this->faker = \Faker\Factory::create();

        // Create mocks for UriInterface and StreamInterface for testing
        $this->mockUri = $this->getMockBuilder('Lwd\Http\Message\UriInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockStream = $this->getMockBuilder('Lwd\Http\Message\StreamInterface')
            ->disableOriginalConstructor()
            ->getMock();

        // Setup test data with Faker
        $this->serverParams = [
            'SERVER_NAME' => $this->faker->domainName,
            'REQUEST_METHOD' => $this->faker->randomElement(['GET', 'POST', 'PUT', 'DELETE']),
            'REMOTE_ADDR' => $this->faker->ipv4
        ];

        $this->cookies = [
            'session_id' => $this->faker->sha1,
            'user_pref' => $this->faker->randomElement(['dark_mode', 'light_mode'])
        ];

        $this->queryParams = [
            'page' => (string)$this->faker->numberBetween(1, 10),
            'sort' => $this->faker->randomElement(['asc', 'desc'])
        ];

        // Create mock uploaded files
        $mockUploadedFile1 = $this->getMockBuilder('Lwd\Http\Message\UploadedFileInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $mockUploadedFile2 = $this->getMockBuilder('Lwd\Http\Message\UploadedFileInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $this->uploadedFiles = [
            'file1' => $mockUploadedFile1,
            'file2' => $mockUploadedFile2
        ];

        $this->parsedBody = [
            'username' => $this->faker->userName,
            'email' => $this->faker->email
        ];

        $this->attributes = [
            'route' => 'user.profile',
            'user_id' => $this->faker->numberBetween(1, 1000)
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
        $method = $this->faker->randomElement(['GET', 'POST', 'PUT', 'DELETE']);
        $protocolVersion = $this->faker->randomElement(['1.0', '1.1', '2.0']);
        $headers = [
            'Content-Type' => [$this->faker->mimeType()],
            'Accept' => [$this->faker->mimeType()]
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
            'theme' => $this->faker->randomElement(['light', 'dark', 'system']),
            'language' => $this->faker->languageCode
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
            'filter' => $this->faker->word,
            'limit' => (string)$this->faker->numberBetween(10, 100)
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

        $newUploadedFile = $this->getMockBuilder('Lwd\Http\Message\UploadedFileInterface')
            ->disableOriginalConstructor()
            ->getMock();

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
            'name' => $this->faker->name,
            'age' => $this->faker->numberBetween(18, 80)
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
        $newParsedBody->name = $this->faker->name;
        $newParsedBody->age = $this->faker->numberBetween(18, 80);

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
        $userId = $this->attributes['user_id'];

        // Test getting existing attribute
        $this->assertEquals($userId, $request->getAttribute('user_id'));
        $this->assertEquals($userId, $request->getAttribute('user_id'), 'Attribute should be immutable');

        // Test getting non-existent attribute with default
        $defaultValue = $this->faker->word;
        $this->assertEquals($defaultValue, $request->getAttribute('non_existent', $defaultValue));
        $this->assertEquals($defaultValue, $request->getAttribute('non_existent', $defaultValue), 'Default should be consistently returned');

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
        $attributeName = 'role';
        $attributeValue = $this->faker->randomElement(['admin', 'user', 'guest']);
        $newRequest = $request->withAttribute($attributeName, $attributeValue);

        // Test immutability - original is unchanged
        $this->assertFalse(isset($request->getAttributes()[$attributeName]));
        $this->assertEquals($this->attributes, $request->getAttributes(), 'Original attributes should be immutable');

        // Test correctness - new instance has new attribute
        $this->assertEquals($attributeValue, $newRequest->getAttribute($attributeName));
        $this->assertEquals($attributeValue, $newRequest->getAttribute($attributeName), 'New attribute should be immutable');

        // Original attributes should still be present in new instance
        $userId = $this->attributes['user_id'];
        $this->assertEquals($userId, $newRequest->getAttribute('user_id'));
        $this->assertEquals($userId, $newRequest->getAttribute('user_id'), 'Existing attribute should be immutable');

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
        $attributeToRemove = 'user_id';
        $userId = $this->attributes['user_id'];
        $newRequest = $request->withoutAttribute($attributeToRemove);

        // Test immutability - original is unchanged
        $this->assertEquals($userId, $request->getAttribute($attributeToRemove));
        $this->assertEquals($userId, $request->getAttribute($attributeToRemove), 'Original attribute should be immutable');
        $this->assertEquals($this->attributes, $request->getAttributes(), 'Original attributes should be immutable');

        // Test correctness - new instance has attribute removed
        $this->assertNull($newRequest->getAttribute($attributeToRemove));
        $this->assertNull($newRequest->getAttribute($attributeToRemove), 'Removed attribute should consistently return null');
        $this->assertFalse(isset($newRequest->getAttributes()[$attributeToRemove]));

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
        $nonExistentAttribute = $this->faker->word . '_' . $this->faker->numberBetween(1000, 9999);
        $newRequest = $request->withoutAttribute($nonExistentAttribute);

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
