<?php

namespace MNC\Http\Encoding;

use Castor\Io\TestReader;
use JsonException;
use PHPUnit\Framework\TestCase;

/**
 * Class JsonTest
 * @package MNC\Http\Encoding
 */
class JsonTest extends TestCase
{
    public function testItDecodesJson(): void
    {
        $reader = TestReader::fromString('{"id":"123456"}');

        $json = new Json($reader);
        self::assertSame(['id' => '123456'], $json->decode());
    }

    public function testItThrowsExceptionOnInvalidJson(): void
    {
        $reader = TestReader::fromString('{"id":"123456", this is invalid json}');

        $json = new Json($reader);
        $this->expectException(JsonException::class);
        $json->decode();
    }
}
