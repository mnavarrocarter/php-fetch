<?php


namespace MNC\Http\Encoding;

/**
 * A JsonReader reads a json string.
 *
 * @package MNC\Http\Encoding
 */
interface JsonReader
{
    /**
     * Reads a whole json string in memory
     *
     * @return string
     */
    public function readAll(): string;
}