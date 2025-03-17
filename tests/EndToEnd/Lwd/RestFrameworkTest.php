<?php

namespace EndToEnd\Lwd;

use PHPUnit_Framework_TestCase;
use Guzzle\Http\Client;

/**
 * @author Johnathan Louie
 */
class RestFrameworkTest extends PHPUnit_Framework_TestCase {

    public function provideBasic() {
        yield ['GET'];
        yield ['POST'];
        yield ['PUT'];
        yield ['PATCH'];
        yield ['DELETE'];
    }

    /**
     * @dataProvider provideBasic
     * @coversNothing
     * @param string $method REST verbs.
     * @return void
     */
    public function testBasic($method) {
        $client = new Client('http://web-server/api');
        $response = $client->createRequest($method, 'a/b/c')->send();
        self::assertTrue($response->isSuccessful());
        self::assertSame('Hello world!', $response->getBody(true));
    }

}
