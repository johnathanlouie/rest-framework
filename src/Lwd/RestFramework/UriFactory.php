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
        // Check if the URI is a string
        if (!is_string($uri)) {
            throw new InvalidArgumentException(sprintf(
                'URI must be a string, %s given.',
                gettype($uri)
            ));
        }

        // Trim whitespace from URI
        $uri = trim($uri);

        // If URI is empty, return an empty instance
        if ($uri === '') {
            return new Uri('');
        }

        // Basic URI validation
        if ($this->hasInvalidCharacters($uri)) {
            throw new InvalidArgumentException(
                'URI contains invalid characters.'
            );
        }

        // Parse the URI components
        $components = parse_url($uri);

        // Check if parsing succeeded
        if ($components === false) {
            throw new InvalidArgumentException(
                'Unable to parse URI: ' . $uri
            );
        }

        // Validate scheme if present
        if (isset($components['scheme'])) {
            $this->validateScheme($components['scheme']);
        }

        // Validate port if present
        if (isset($components['port'])) {
            $this->validatePort($components['port']);
        }

        // Create the URI instance
        return new Uri($uri);
    }

    /**
     * Check if a URI string contains invalid characters.
     * 
     * According to RFC 3986, certain characters are not permitted in URIs, including
     * whitespace and control characters. These must be percent-encoded if they are to
     * be included in a valid URI. This validation prevents security issues and ensures
     * consistent behavior across different systems.
     *
     * @param string $uri The URI to validate
     * @return bool True if contains invalid chars, false otherwise
     * @link https://tools.ietf.org/html/rfc3986#section-2 RFC 3986 Section 2: Characters
     */
    private function hasInvalidCharacters($uri)
    {
        // Check for unencoded spaces and control characters
        return preg_match('/\s|[\x00-\x1F\x7F]/', $uri) === 1;
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
     * @throws InvalidArgumentException If the scheme is invalid
     * @link https://tools.ietf.org/html/rfc3986#section-3.1 RFC 3986 Section 3.1
     */
    private function validateScheme($scheme)
    {
        // Scheme must be a string
        if (!is_string($scheme)) {
            throw new InvalidArgumentException(
                'Scheme must be a string'
            );
        }

        // RFC 3986 Section 3.1: Scheme must start with a letter and consist of letters, digits, plus, period, or hyphen
        if (!preg_match('/^[a-zA-Z][a-zA-Z0-9+\-.]*$/', $scheme)) {
            throw new InvalidArgumentException(
                'Scheme "' . $scheme . '" does not conform to RFC 3986 section 3.1.'
            );
        }
    }

    /**
     * Validate a URI port.
     * 
     * Port numbers are limited by the TCP/IP specification to the range 1-65535.
     * This validation ensures that URIs created by this factory will always have
     * valid port numbers that can be used in actual network connections.
     *
     * @param int|string $port The port to validate
     * @throws InvalidArgumentException If the port is invalid
     */
    private function validatePort($port)
    {
        // Port must be an integer between 1 and 65535
        $port = (int) $port;
        if ($port < 1 || $port > 65535) {
            throw new InvalidArgumentException(
                sprintf('Invalid port: %d. Must be between 1 and 65535', $port)
            );
        }
    }
}
