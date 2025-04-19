<?php

namespace Tests\Unit\Lwd\RestFramework;

use InvalidArgumentException;
use Lwd\RestFramework\UriFactory;
use Lwd\Http\Message\UriInterface;
use PHPUnit_Framework_TestCase;

/**
 * Unit tests for the UriFactory class.
 *
 * Tests UriFactory's ability to create properly validated URI objects
 * according to RFC 3986 specifications, including various edge cases
 * and special formats.
 */
class UriFactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var UriFactory
     */
    private $uriFactory;

    /**
     * Set up the test environment.
     */
    protected function setUp()
    {
        $this->uriFactory = new UriFactory();
    }

    /**
     * Test creating an empty URI.
     */
    public function testCreateEmptyUri()
    {
        $uri = $this->uriFactory->createUri('');

        $this->assertInstanceOf(UriInterface::class, $uri);
        $this->assertSame('', (string)$uri);
    }

    /**
     * Test creating a URI with whitespace that should be trimmed.
     */
    public function testCreateUriWithWhitespace()
    {
        $uri = $this->uriFactory->createUri('  https://example.com  ');

        $this->assertInstanceOf(UriInterface::class, $uri);
        $this->assertSame('https://example.com', (string)$uri);
    }

    /**
     * Test creating a URI with all components.
     */
    public function testCreateCompleteUri()
    {
        $uri = $this->uriFactory->createUri('https://user:pass@example.com:8080/path?query=value#fragment');

        $this->assertInstanceOf(UriInterface::class, $uri);
        $this->assertSame('https', $uri->getScheme());
        $this->assertSame('user:pass', $uri->getUserInfo());
        $this->assertSame('example.com', $uri->getHost());
        $this->assertSame(8080, $uri->getPort());
        $this->assertSame('/path', $uri->getPath());
        $this->assertSame('query=value', $uri->getQuery());
        $this->assertSame('fragment', $uri->getFragment());
    }

    /**
     * Test creating URIs with different valid schemes.
     *
     * @dataProvider validSchemeProvider
     */
    public function testCreateUriWithValidScheme($scheme)
    {
        $uri = $this->uriFactory->createUri($scheme . '://example.com');

        $this->assertInstanceOf(UriInterface::class, $uri);
        $this->assertSame($scheme, $uri->getScheme());
    }

    /**
     * Data provider for valid URI schemes per RFC 3986.
     */
    public function validSchemeProvider()
    {
        return [
            ['http'],
            ['https'],
            ['ftp'],
            ['git'],
            ['ssh'],
            ['file'],
            ['mailto'],
            ['custom'],
            ['a'],                 // Minimum length
            ['scheme123'],         // With numbers
            ['s-c-h-e-m-e'],      // With hyphens
            ['scheme.name'],       // With period
            ['scheme+name'],       // With plus
        ];
    }

    /**
     * Test creating URIs with invalid schemes.
     *
     * @dataProvider invalidSchemeProvider
     */
    public function testCreateUriWithInvalidScheme($scheme)
    {
        $this->setExpectedException(InvalidArgumentException::class);
        $this->uriFactory->createUri($scheme . '://example.com');
    }

    /**
     * Data provider for invalid URI schemes per RFC 3986.
     */
    public function invalidSchemeProvider()
    {
        return [
            ['1scheme'],          // Can't start with number
            ['scheme:'],          // Contains invalid character
            ['sc^heme'],          // Contains invalid character
            ['scheme name'],      // Contains space
            ['-scheme'],          // Can't start with hyphen
            ['+scheme'],          // Can't start with plus
            ['.scheme'],          // Can't start with period
            ['схема'],            // Non-ASCII characters
        ];
    }

    /**
     * Test creating URI with valid port numbers.
     *
     * @dataProvider validPortProvider
     */
    public function testCreateUriWithValidPort($port, $scheme, $expectedPort)
    {
        $uri = $this->uriFactory->createUri($scheme . '://example.com:' . $port);

        $this->assertInstanceOf(UriInterface::class, $uri);
        $this->assertSame($expectedPort, $uri->getPort());
    }

    /**
     * Data provider for valid port numbers.
     * According to PSR-7, standard ports should be represented as null.
     */
    public function validPortProvider()
    {
        return [
            [1, 'http', 1],           // Non-standard port
            [80, 'http', null],       // Standard HTTP port
            [443, 'https', null],     // Standard HTTPS port
            [8080, 'http', 8080],     // Non-standard port
            [65535, 'http', 65535],   // Maximum valid port
        ];
    }

    /**
     * Test creating URI with invalid port numbers.
     *
     * @dataProvider invalidPortProvider
     */
    public function testCreateUriWithInvalidPort($port)
    {
        $this->setExpectedException(InvalidArgumentException::class);
        $this->uriFactory->createUri('http://example.com:' . $port);
    }

    /**
     * Data provider for invalid port numbers.
     */
    public function invalidPortProvider()
    {
        return [
            [0],        // Below minimum
            [-1],       // Negative
            [65536],    // Above maximum
            [999999],   // Far above maximum
        ];
    }

    /**
     * Test creating URI with invalid characters.
     */
    public function testCreateUriWithInvalidCharacters()
    {
        $this->setExpectedException(InvalidArgumentException::class);
        $this->uriFactory->createUri("http://example.com/path with spaces");
    }

    /**
     * Test creating URI with non-string input.
     *
     * @dataProvider invalidTypeProvider
     */
    public function testCreateUriWithInvalidType($input)
    {
        $this->setExpectedException(InvalidArgumentException::class);
        $this->uriFactory->createUri($input);
    }

    /**
     * Data provider for invalid input types.
     */
    public function invalidTypeProvider()
    {
        return [
            [null],
            [123],
            [1.23],
            [true],
            [[]],
            [new \stdClass()],
        ];
    }

    /**
     * Test creating URI with a malformed URI that can't be parsed.
     */
    public function testCreateUriWithUnparsableUri()
    {
        $this->setExpectedException(InvalidArgumentException::class);
        // This URI is malformed and cannot be properly parsed
        $this->uriFactory->createUri('http:///example.com');
    }

    /**
     * Test URIs with properly percent-encoded characters.
     *
     * @dataProvider validPercentEncodingProvider
     */
    public function testCreateUriWithValidPercentEncoding($uri, $expectedPath)
    {
        $uri = $this->uriFactory->createUri($uri);

        $this->assertInstanceOf(UriInterface::class, $uri);
        $this->assertSame($expectedPath, $uri->getPath());
    }

    /**
     * Data provider for URIs with valid percent encoding.
     */
    public function validPercentEncodingProvider()
    {
        return [
            ['http://example.com/path%20with%20spaces', '/path%20with%20spaces'],
            ['http://example.com/%E2%82%AC', '/%E2%82%AC'], // Euro sign €
            ['http://example.com/%F0%9F%8D%95', '/%F0%9F%8D%95'], // Pizza emoji 🍕
            ['http://example.com/a%2Fb%2Fc', '/a%2Fb%2Fc'], // Encoded slashes
        ];
    }

    /**
     * Test URIs with invalid percent-encoded characters.
     *
     * @dataProvider invalidPercentEncodingProvider
     */
    public function testCreateUriWithInvalidPercentEncoding($uri)
    {
        $this->setExpectedException(InvalidArgumentException::class);
        $this->uriFactory->createUri($uri);
    }

    /**
     * Data provider for URIs with invalid percent encoding.
     */
    public function invalidPercentEncodingProvider()
    {
        return [
            ['http://example.com/%'], // Incomplete percent encoding
            ['http://example.com/%A'], // Incomplete percent encoding
            ['http://example.com/%G'], // Incomplete percent encoding
            ['http://example.com/%G0'], // Invalid hex digit
            ['http://example.com/%0G'], // Invalid hex digit
        ];
    }

    /**
     * Test URIs with empty components.
     *
     * @dataProvider emptyComponentsProvider
     */
    public function testCreateUriWithEmptyComponents($uri, $expectedPath, $expectedQuery, $expectedFragment)
    {
        $uri = $this->uriFactory->createUri($uri);

        $this->assertInstanceOf(UriInterface::class, $uri);
        $this->assertSame($expectedPath, $uri->getPath());
        $this->assertSame($expectedQuery, $uri->getQuery());
        $this->assertSame($expectedFragment, $uri->getFragment());
    }

    /**
     * Data provider for URIs with empty components.
     */
    public function emptyComponentsProvider()
    {
        return [
            ['http://example.com', '', '', ''], // No path, query, or fragment
            ['http://example.com/', '/', '', ''], // Root path only
            ['http://example.com/?', '/', '', ''], // Empty query
            ['http://example.com/#', '/', '', ''], // Empty fragment
            ['http://example.com/?#', '/', '', ''], // Empty query and fragment
            ['http://example.com/#?', '/', '', '?'], // Fragment contains question mark
        ];
    }

    /**
     * Test relative URI references (those without a scheme).
     *
     * @dataProvider relativeReferenceProvider
     */
    public function testCreateRelativeReference($uri, $expectedScheme, $expectedHost, $expectedPath)
    {
        $uri = $this->uriFactory->createUri($uri);

        $this->assertInstanceOf(UriInterface::class, $uri);
        $this->assertSame($expectedScheme, $uri->getScheme());
        $this->assertSame($expectedHost, $uri->getHost());
        $this->assertSame($expectedPath, $uri->getPath());
    }

    /**
     * Data provider for relative URI references.
     */
    public function relativeReferenceProvider()
    {
        return [
            ['/path/to/resource', '', '', '/path/to/resource'], // Absolute path
            ['path/to/resource', '', '', 'path/to/resource'], // Relative path
            ['//example.com/path', '', 'example.com', '/path'], // Network-path reference
            ['/path?query=value', '', '', '/path'], // Path with query
            ['?query=value', '', '', ''], // Query only
            ['#fragment', '', '', ''], // Fragment only
        ];
    }

    /**
     * Test special case schemes like file:/// with empty authority.
     */
    public function testCreateFileUriWithEmptyAuthority()
    {
        $uri = $this->uriFactory->createUri('file:///path/to/file.txt');

        $this->assertInstanceOf(UriInterface::class, $uri);
        $this->assertSame('file', $uri->getScheme());
        $this->assertSame('', $uri->getAuthority());
        $this->assertSame('/path/to/file.txt', $uri->getPath());
    }

    /**
     * Test file URIs with a host.
     */
    public function testCreateFileUriWithHost()
    {
        $uri = $this->uriFactory->createUri('file://localhost/path/to/file.txt');

        $this->assertInstanceOf(UriInterface::class, $uri);
        $this->assertSame('file', $uri->getScheme());
        $this->assertSame('localhost', $uri->getHost());
        $this->assertSame('/path/to/file.txt', $uri->getPath());
    }

    /**
     * Test authority-less URI schemes like mailto:
     */
    public function testCreateAuthorityLessUri()
    {
        $uri = $this->uriFactory->createUri('mailto:user@example.com');

        $this->assertInstanceOf(UriInterface::class, $uri);
        $this->assertSame('mailto', $uri->getScheme());
        $this->assertSame('', $uri->getAuthority());
        $this->assertSame('user@example.com', $uri->getPath());
    }

    /**
     * Test news URI with authority-like syntax but actually part of the path.
     */
    public function testCreateNewsUri()
    {
        $uri = $this->uriFactory->createUri('news:comp.infosystems.www.servers.unix');

        $this->assertInstanceOf(UriInterface::class, $uri);
        $this->assertSame('news', $uri->getScheme());
        $this->assertSame('', $uri->getAuthority());
        $this->assertSame('comp.infosystems.www.servers.unix', $uri->getPath());
    }

    /**
     * Test creating a fragment-only URI.
     */
    public function testCreateFragmentOnlyUri()
    {
        $uri = $this->uriFactory->createUri('#section');

        $this->assertInstanceOf(UriInterface::class, $uri);
        $this->assertSame('', $uri->getScheme());
        $this->assertSame('', $uri->getPath());
        $this->assertSame('section', $uri->getFragment());
    }

    /**
     * Test behavior with extremely long URI components.
     *
     * @dataProvider longComponentProvider
     */
    public function testCreateUriWithLongComponents($uri)
    {
        $uri = $this->uriFactory->createUri($uri);
        $this->assertInstanceOf(UriInterface::class, $uri);
    }

    /**
     * Data provider for URIs with extremely long components.
     */
    public function longComponentProvider()
    {
        // Create long strings for testing
        $longScheme = 'a' . str_repeat('b', 50); // Long but valid scheme
        $longPath = '/' . str_repeat('path/', 100);
        $longQuery = 'param=' . str_repeat('value', 100);
        $longFragment = str_repeat('section', 100);

        return [
            ["http://example.com$longPath"],
            ["http://example.com/?$longQuery"],
            ["http://example.com/#$longFragment"],
            ["$longScheme://example.com"],
        ];
    }
}
