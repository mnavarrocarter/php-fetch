<?php

namespace MNC\Http;

use PHPUnit\Framework\TestCase;

/**
 * Class HeadersTest
 * @package MNC\Http
 */
class HeadersTest extends TestCase
{
    public function testItConvertsToArrayProperly(): void
    {
        $headers = Headers::fromMap([
            'Content-Type' => 'application/json',
            'Content-Length' => '3532',
            'Server' => 'nginx'
        ]);

        $lines = $headers->toArray();

        self::assertSame('content-type: application/json', $lines[0]);
        self::assertSame('content-length: 3532', $lines[1]);
        self::assertSame('server: nginx', $lines[2]);
        self::assertCount(3, $lines);
    }

    public function testFilter(): void
    {
        $headers = Headers::fromMap([
            'Content-Type' => 'application/json',
            'Content-Length' => '3532',
            'Server' => 'nginx'
        ]);

        $headers = $headers->filter(fn($value, $name) => $name !== 'server');

        self::assertCount(2, $headers);
        self::assertArrayHasKey('content-type', $headers);
        self::assertArrayHasKey('content-length', $headers);
        self::assertArrayNotHasKey('server', $headers);
    }
}
