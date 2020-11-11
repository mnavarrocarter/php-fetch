<?php

namespace MNC\Http\Encoding;

use JsonException;
use MNC\Http\Io\Reader;
use MNC\Http\Io\ReaderError;
use function MNC\Http\buffer;

/**
 * Class Json
 * @package MNC\Http\Io
 */
final class Json implements Reader, JsonDecoder, JsonReader
{
    /**
     * @var Reader
     */
    private Reader $reader;

    /**
     * Json constructor.
     * @param Reader $reader
     */
    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * @return string
     * @throws ReaderError
     */
    public function readAll(): string
    {
        return buffer($this->reader);
    }

    /**
     * @return array
     * @throws JsonException
     * @throws ReaderError
     */
    public function decode(): array
    {
        return json_decode($this->readAll(), true, 512, JSON_THROW_ON_ERROR);
    }

    public function read(int $bytes = self::DEFAULT_CHUNK_SIZE): ?string
    {
        return $this->reader->read($bytes);
    }
}