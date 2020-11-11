<?php

namespace MNC\Http;

use PHPUnit\Framework\TestCase;

/**
 * Class ResponseTest
 * @package MNC\Http
 */
class ResponseTest extends TestCase
{
    public function testItParsesFirstLine(): void
    {
        $headers = $this->createStub(Headers::class);
        $response = Response::fromFirstLine('HTTP/1.1 404 NOT FOUND', $headers);
    }
}
