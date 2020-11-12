<?php

namespace MNC\Http\Encoding;

use JsonException;
use MNC\Http\Io\Reader;
use PHPUnit\Framework\TestCase;

class JsonTest extends TestCase
{
    public function testItReadsFromReader(): void
    {
        $jsonString = '{"id":"123456"}';
        $reader = $this->createMock(Reader::class);

        $reader->expects(self::exactly(2))
            ->method('read')
            ->with(Reader::DEFAULT_CHUNK_SIZE)
            ->willReturnOnConsecutiveCalls($jsonString, null);

        $json = new Json($reader);
        self::assertSame($jsonString, $json->read());
        self::assertNull($json->read());
    }

    public function testItDecodesJson(): void
    {
        $jsonString = '{"id":"123456"}';
        $reader = $this->createMock(Reader::class);

        $reader->expects(self::exactly(2))
            ->method('read')
            ->with(Reader::DEFAULT_CHUNK_SIZE)
            ->willReturnOnConsecutiveCalls($jsonString, null);

        $json = new Json($reader);
        self::assertSame(['id' => '123456'], $json->decode());
    }

    public function testItThrowsExceptionOnInvalidJson(): void
    {
        $jsonString = '{"id":"123456", this is invalid json}';
        $reader = $this->createMock(Reader::class);

        $reader->expects(self::exactly(2))
            ->method('read')
            ->with(Reader::DEFAULT_CHUNK_SIZE)
            ->willReturnOnConsecutiveCalls($jsonString, null);

        $json = new Json($reader);
        $this->expectException(JsonException::class);
        $json->decode();
    }
}
