<?php


namespace MNC\Http;

use MNC\Http\Encoding\JsonDecoder;
use PHPUnit\Framework\TestCase;

/**
 * Class FetchFunctionalTest
 * @package MNC\Http
 */
class FetchFunctionalTest extends TestCase
{
    public function testItFetchesRootHtml(): void
    {
        $response = fetch('http://127.0.0.1:5488');

        self::assertSame('1.1', $response->status()->protocolVersion());
        self::assertSame(200, $response->status()->code());
        self::assertSame('OK', $response->status()->reasonPhrase());
        self::assertTrue($response->headers()->contains('Content-Type', 'text/html'));
        self::assertTrue($response->headers()->has('etag'));
        self::assertSame('355', $response->headers()->get('Content-Length'));
        $html = file_get_contents(__DIR__ . '/static/index.html');
        self::assertSame($html, buffer($response->body()));
    }

    public function testItThrowsSocketErrorOnRefusedConnection(): void
    {
        $this->expectException(SocketError::class);
        fetch('http://127.0.0.1:5489');
    }

    public function testItThrowsSocketErrorOnInvalidDomainName(): void
    {
        $this->expectException(SocketError::class);
        fetch('http://i-am-unresolvable.dev');
    }

    public function testItCanChangeProtocol(): void
    {
        $response = fetch('http://127.0.0.1:5488', [
            'protocol_version' => '1.0'
        ]);

        self::assertSame('1.0', $response->status()->protocolVersion());
    }

    public function testItFollowsRedirectsByDefault(): void
    {
        $response = fetch('http://127.0.0.1:5488/login', [
            'method' => 'POST'
        ]);

        self::assertSame(200, $response->status()->code());
        self::assertTrue($response->headers()->contains('Content-Type', 'application/json'));
        self::assertInstanceOf(Redirected::class, $response);
        self::assertCount(1, $response->previousResponses());
    }

    public function testFollowingRedirectsCanBeDisabled(): void
    {
        $response = fetch('http://127.0.0.1:5488/login', [
            'method' => 'POST',
            'follow_redirects' => false
        ]);

        self::assertSame(302, $response->status()->code());
        self::assertNotInstanceOf(Redirected::class, $response);
    }

    public function testItDecodesJson(): void
    {
        $response = fetch('http://127.0.0.1:5488/user.json');
        $body = $response->body();
        self::assertTrue($response->headers()->contains('Content-Type', 'application/json'));
        self::assertInstanceOf(JsonDecoder::class, $body);
        self::assertSame([
            'id' => 'd1129f05-45e3-47ba-be0e-cffba7fdf9f6',
            'name' => 'John Doe',
            'email' => 'jdoe@example.com'
        ], $body->decode());
    }

    public function testItThrowsProtocolError(): void
    {
        $this->expectException(ProtocolError::class);
        fetch('http://127.0.0.1:5488/does/not/exist');
    }

    public function testProtocolErrorContainsAResponse(): void
    {
        try {
            fetch('http://127.0.0.1:5488/does/not/exist');
        } catch (ProtocolError $exception) {
            $response = $exception->getResponse();
            self::assertTrue($response->status()->isCode(404));
        }
    }
}
