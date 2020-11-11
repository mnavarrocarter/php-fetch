<?php


namespace MNC\Http\Io;

/**
 * Interface Reader
 * @package MNC\Http\Io
 */
interface Reader
{
    public const DEFAULT_CHUNK_SIZE = 4096;

    /**
     * Reads raw bytes from the source
     *
     * Returns null on EOF
     *
     * @param int $bytes
     * @return string|null
     *
     * @throws ReaderError when contents cannot be read
     */
    public function read(int $bytes = self::DEFAULT_CHUNK_SIZE): ?string;
}