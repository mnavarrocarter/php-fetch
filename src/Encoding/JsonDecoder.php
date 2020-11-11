<?php


namespace MNC\Http\Encoding;

use JsonException;

/**
 * A JsonDecoder decodes json data.
 *
 * @package MNC\Http\Io
 */
interface JsonDecoder
{
    /**
     * Decodes a json string into an associative array
     *
     * @return array
     * @throws JsonException when parsing fails
     */
    public function decode(): array;
}