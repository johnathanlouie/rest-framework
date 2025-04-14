<?php

namespace Lwd\RestFramework;

use Lwd\Http\Message\UriFactoryInterface;
use Lwd\Http\Message\UriInterface;
use InvalidArgumentException;

/**
 * Factory for creating URI instances.
 * 
 * This implementation creates instances of the Uri class from the Lwd\RestFramework 
 * following PSR-17 specifications. The factory is responsible for constructing
 * well-formed URI objects that conform to RFC 3986.
 *
 * By validating URIs at creation time rather than during usage, this factory
 * ensures that all URI objects in the system are valid and complete, which simplifies
 * error handling throughout the application and promotes cleaner architecture.
 *
 * URI Structure according to RFC 3986:
 * 
 *     URI = scheme ":" hier-part [ "?" query ] [ "#" fragment ]
 *     
 *     hier-part = "//" authority path
 *                 / path
 *     
 *     authority = [ userinfo "@" ] host [ ":" port ]
 * 
 * Visual breakdown of URI components:
 * 
 *     http://username:password@example.com:8042/over/there?name=ferret#nose
 *     \__/   \________________/\_________/ \__/\________/ \_________/ \__/
 *      |              |             |       |      |           |        |
 *    scheme       userinfo         host    port   path       query   fragment
 *      |       \____________________|_____/    |                  |
 *      |                      |                |                  |
 *      |                  authority           path              query
 *      |_________________________________________________________________|
 *                                     |
 *                                     |
 *                                     URI
 *
 * Important: The ":" and "//" are distinct syntactic elements, not a single "://" entity:
 * - The ":" (colon) is a delimiter that marks the end of the scheme
 * - The "//" (double slash) is a separate indicator that an authority component follows
 * 
 * These are separate elements because some URI schemes use the colon without the double slash:
 *   - "http://example.com" - uses both the colon and double slash
 *   - "mailto:user@example.com" - uses only the colon, without double slash
 * 
 * Special case examples:
 * 
 * 1. File URI with empty authority:
 *     file:///path/to/file.txt
 *     \__/ \/ \_____________/
 *      |    |        |
 *   scheme  |       path
 *           |
 *      empty authority
 * 
 * 2. Mailto URI with no authority component:
 *     mailto:user@example.com
 *     \____/ \____________/
 *       |          |
 *     scheme      path
 *
 * @package Lwd\RestFramework
 * @see https://www.php-fig.org/psr/psr-17/ PSR-17: HTTP Factories
 * @link https://tools.ietf.org/html/rfc3986 RFC 3986: Uniform Resource Identifier
 * @link https://tools.ietf.org/html/rfc8089 RFC 8089: The "file" URI Scheme
 */
class UriFactory implements UriFactoryInterface
{
    /**
     * Create a new URI.
     *
     * This method validates the provided URI string according to RFC 3986
     * before creating a new Uri instance. It ensures the URI has a valid
     * structure and components before proceeding with object creation.
     *
     * Validation is performed at creation time to maintain system integrity and
     * ensure that all URI objects in the application represent properly formed URIs.
     * This approach prevents downstream errors by catching malformed URIs early.
     *
     * @param string $uri The URI string
     *
     * @return UriInterface A new immutable instance of a URI
     *
     * @throws InvalidArgumentException If the given URI cannot be parsed or has invalid components
     * @link https://tools.ietf.org/html/rfc3986 RFC 3986: Uniform Resource Identifier
     */
    public function createUri($uri = '')
    {
        if (!is_string($uri)) {
            throw new InvalidArgumentException(sprintf('URI must be a string, %s given', gettype($uri)));
        }

        $uri = trim($uri);

        if ($uri === '') {
            return new Uri('');
        }

        if (self::hasInvalidCharacters($uri)) {
            throw new InvalidArgumentException('Invalid characters in URI');
        }
        }

        // Parse the URI components
        $components = parse_url($uri);

        // Check if parsing succeeded
        if ($components === false) {
            throw new InvalidArgumentException(
                'Unable to parse URI: ' . $uri
            );
        }

        if (isset($components['scheme']) && !self::isValidScheme($components['scheme'])) {
            throw new InvalidArgumentException("Invalid URI scheme {$components['scheme']}");
        }

        if (isset($components['port']) && !self::isValidPort($components['port'])) {
            throw new InvalidArgumentException("Port must be between 1 and 65535, got {$components['port']}");
        }

        return new Uri($uri);
    }

    /**
     * Check if a URI string contains invalid characters.
     * 
     * According to RFC 3986, URIs must only contain characters from the US-ASCII character set,
     * and even within ASCII, many characters are restricted:
     * 
     * 1. Control characters (0x00-0x1F, 0x7F): Non-printable characters are not allowed
     * 2. Whitespace characters: Spaces, tabs, line feeds, etc. must be percent-encoded
     * 3. Unsafe characters: <, >, ", `, \, ^, {, }, |, etc. must be percent-encoded
     * 4. Reserved characters when not used for their reserved purpose: :, /, ?, #, [, ], etc.
     * 
     * Valid characters in URIs include:
     * - Unreserved: A-Z, a-z, 0-9, hyphen, period, underscore, and tilde (0x41-0x5A, 0x61-0x7A, 0x30-0x39, 0x2D, 0x2E, 0x5F, 0x7E)
     * - Reserved (when used properly): :, /, ?, #, [, ], @, !, $, &, ', (, ), *, +, ,, ;, = 
     *   (0x3A, 0x2F, 0x3F, 0x23, 0x5B, 0x5D, 0x40, 0x21, 0x24, 0x26, 0x27, 0x28, 0x29, 0x2A, 0x2B, 0x2C, 0x3B, 0x3D)
     * 
     * Additionally, any non-ASCII characters (such as Unicode/UTF-8) must be percent-encoded 
     * before inclusion in a URI. All such characters must be properly percent-encoded to 
     * their UTF-8 byte sequences to ensure proper interpretation across all systems.
     *
     * Non-conforming URIs can cause interoperability issues, security vulnerabilities,
     * and inconsistent behavior across different systems.
     *
     * @param string $uri The URI to validate
     * @return bool True if the URI contains invalid characters, false otherwise
     * @link https://tools.ietf.org/html/rfc3986#section-2 RFC 3986 Section 2: Characters
     */
    private static function hasInvalidCharacters($uri)
    {
        return preg_match('/[^\x21\x24-\x29\x2A-\x3B\x3D\x3F-\x5B\x5D\x5F\x61-\x7A\7E]/', $uri) > 0;
    }

    /**
     * Check for invalid percent encoding in a URI string.
     *
     * According to RFC 3986, percent-encoded octets must use the format %XX
     * where X is a hexadecimal digit (0-9, A-F). This validation ensures that
     * any percent encoding in the URI is properly formed.
     * 
     * @param string $uri The URI to check
     * @return bool True if invalid percent encoding is found, false otherwise
     * @link https://tools.ietf.org/html/rfc3986#section-2.1 RFC 3986 Section 2.1: Percent-Encoding
     */
    private static function hasInvalidPercentEncoding($uri)
    {
        // Find any % character that is not followed by two hexadecimal digits
        return preg_match('/%(?![0-9A-Fa-f]{2})/', $uri) > 0;
    }

    /**
     * Validate a URI scheme according to RFC 3986 section 3.1.
     * 
     * A scheme must begin with a letter and can be followed by any combination of
     * letters, digits, plus ("+"), period ("."), or hyphen ("-"). These restrictions
     * are designed to ensure interoperability and avoid conflicts with existing and
     * future URI schemes.
     *
     * Format: scheme = ALPHA *( ALPHA / DIGIT / "+" / "-" / "." )
     *
     * In a complete URI, the scheme is terminated with a colon (":") delimiter, 
     * which separates it from the rest of the URI. However, the colon is not part 
     * of the scheme itself and should not be included when validating the scheme.
     * 
     * After the scheme and colon, the URI may have:
     * 1. "//" followed by an authority component (e.g., "http://example.com")
     * 2. A path component directly (e.g., "mailto:user@example.com")
     * 
     * The colon and double slashes are distinct syntactic elements:
     * - ":" marks the end of the scheme
     * - "//" (when present) indicates the beginning of an authority component
     * 
     * They are not a single "://" entity, which is why some schemes like "mailto:"
     * have the colon but not the double slashes.
     *
     * When "//" is present, it indicates that an authority component follows.
     * The authority may be empty (as in "file:///path"), which results in three
     * consecutive slashes when followed by an absolute path. This is not an error
     * but a valid URI structure with an empty authority component.
     *
     * Examples of valid schemes:
     *   - "http" in "http://example.com"
     *   - "https" in "https://example.com"
     *   - "file" in "file:///usr/local/bin/script.sh" (note the three slashes: scheme + empty authority + absolute path)
     *   - "mailto" in "mailto:user@example.com" (no authority component)
     *   - "custom-scheme.123" (custom scheme with allowed special characters)
     *
     * Examples of invalid schemes:
     *   - "1http" (cannot start with a digit)
     *   - "http:" (the colon is not part of the scheme)
     *   - "http scheme" (contains space)
     *
     * Rather than maintaining a whitelist of allowed schemes, this validation follows
     * the RFC syntax rules, which allows for extensibility while maintaining compliance
     * with standards.
     *
     * @param string $scheme The scheme to validate
     * @return bool True if the scheme is valid, false otherwise
     * @link https://tools.ietf.org/html/rfc3986#section-3.1 RFC 3986 Section 3.1
     */
    private static function isValidScheme($scheme)
    {
        return preg_match('/^[a-zA-Z][a-zA-Z0-9+.-]*$/', $scheme) === 1;
    }

    /**
     * Validate a URI port.
     * 
     * Port numbers are limited by the TCP/IP specification to the range 1-65535.
     * This validation ensures that URIs created by this factory will always have
     * valid port numbers that can be used in actual network connections.
     *
     * Port numbers are 16-bit unsigned integers (0-65535), but port 0 is reserved
     * and not available for use in URIs. Therefore, only port numbers 1-65535 are
     * considered valid for URI authority components.
     *
     * @param int|string $port The port to validate
     * @return bool True if the port is valid, false otherwise
     * @link https://tools.ietf.org/html/rfc793 RFC 793: Transmission Control Protocol
     */
    private static function isValidPort($port)
    {
        $port = (int) $port;
        return 1 <= $port && $port <= 65535;
    }
}
