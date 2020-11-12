<?php

namespace MNC\Http;

use PHPUnit\Framework\TestCase;

class StandardHeadersTest extends TestCase
{
    public function testItReturnsDateTimeOnLastModified(): void
    {
        $headersMock = $this->createMock(Headers::class);

        $headersMock->expects(self::once())
            ->method('has')
            ->with('Last-Modified')
            ->willReturn(true);
        $headersMock->expects(self::once())
            ->method('get')
            ->with('Last-Modified')
            ->willReturn('Wed, 11 Nov 2020 23:25:06 GMT');

        $stdHeaders = new StandardHeaders($headersMock);
        self::assertInstanceOf(\DateTimeImmutable::class, $stdHeaders->getLastModified());
    }

    public function testItReturnsNullOnMalformedLastModified(): void
    {
        $headersMock = $this->createMock(Headers::class);

        $headersMock->expects(self::once())
            ->method('has')
            ->with('Last-Modified')
            ->willReturn(true);
        $headersMock->expects(self::once())
            ->method('get')
            ->with('Last-Modified')
            ->willReturn('This is not a valid date');

        $stdHeaders = new StandardHeaders($headersMock);
        self::assertNull($stdHeaders->getLastModified());
    }

    public function testItReturnsNullOnLastModified(): void
    {
        $headersMock = $this->createMock(Headers::class);

        $headersMock->expects(self::once())
            ->method('has')
            ->with('Last-Modified')
            ->willReturn(false);

        $stdHeaders = new StandardHeaders($headersMock);
        self::assertNull($stdHeaders->getLastModified());
    }

    public function testItReturnsDateTimeOnExpires(): void
    {
        $headersMock = $this->createMock(Headers::class);

        $headersMock->expects(self::once())
            ->method('has')
            ->with('Expires')
            ->willReturn(true);
        $headersMock->expects(self::once())
            ->method('get')
            ->with('Expires')
            ->willReturn('Wed, 11 Nov 2020 23:25:06 GMT');

        $stdHeaders = new StandardHeaders($headersMock);
        self::assertInstanceOf(\DateTimeImmutable::class, $stdHeaders->getExpires());
    }

    public function testItReturnsNullOnMalformedExpires(): void
    {
        $headersMock = $this->createMock(Headers::class);

        $headersMock->expects(self::once())
            ->method('has')
            ->with('Expires')
            ->willReturn(true);
        $headersMock->expects(self::once())
            ->method('get')
            ->with('Expires')
            ->willReturn('This is not a valid date');

        $stdHeaders = new StandardHeaders($headersMock);
        self::assertNull($stdHeaders->getExpires());
    }

    public function testItReturnsNullOnExpires(): void
    {
        $headersMock = $this->createMock(Headers::class);

        $headersMock->expects(self::once())
            ->method('has')
            ->with('Expires')
            ->willReturn(false);

        $stdHeaders = new StandardHeaders($headersMock);
        self::assertNull($stdHeaders->getExpires());
    }

    public function testItReturnsIntegerOnContentLength(): void
    {
        $headersMock = $this->createMock(Headers::class);

        $headersMock->expects(self::once())
            ->method('has')
            ->with('Content-Length')
            ->willReturn(true);
        $headersMock->expects(self::once())
            ->method('get')
            ->with('Content-Length')
            ->willReturn('34325');

        $stdHeaders = new StandardHeaders($headersMock);
        self::assertSame(34325, $stdHeaders->getContentLength());
    }

    public function testItReturnsNullOnContentLength(): void
    {
        $headersMock = $this->createMock(Headers::class);

        $headersMock->expects(self::once())
            ->method('has')
            ->with('Content-Length')
            ->willReturn(false);

        $stdHeaders = new StandardHeaders($headersMock);
        self::assertNull($stdHeaders->getContentLength());
    }

    public function testItReturnsIntegerOnContentType(): void
    {
        $headersMock = $this->createMock(Headers::class);

        $headersMock->expects(self::once())
            ->method('has')
            ->with('Content-Type')
            ->willReturn(true);
        $headersMock->expects(self::once())
            ->method('get')
            ->with('Content-Type')
            ->willReturn('application/json;charset=utf=8');

        $stdHeaders = new StandardHeaders($headersMock);
        self::assertSame('application/json', $stdHeaders->getContentType());
    }

    public function testItReturnsNullOnContentType(): void
    {
        $headersMock = $this->createMock(Headers::class);

        $headersMock->expects(self::once())
            ->method('has')
            ->with('Content-Type')
            ->willReturn(false);

        $stdHeaders = new StandardHeaders($headersMock);
        self::assertNull($stdHeaders->getContentType());
    }
}
