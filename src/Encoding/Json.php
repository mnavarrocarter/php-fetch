<?php

/*
 * This file is part of the https://github.com/mnavarrocarter/php-fetch project.
 * (c) Matías Navarro-Carter <mnavarrocarter@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MNC\Http\Encoding;

use JsonException;
use function MNC\Http\buffer;
use MNC\Http\Io\Reader;
use MNC\Http\Io\ReaderError;

/**
 * Class Json.
 */
final class Json implements Reader, JsonDecoder
{
    private Reader $reader;

    /**
     * Json constructor.
     */
    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * @return array<mixed, mixed>
     *
     * @throws JsonException
     * @throws ReaderError
     */
    public function decode(): array
    {
        return json_decode(buffer($this->reader), true, 512, JSON_THROW_ON_ERROR);
    }

    public function read(int $bytes = self::DEFAULT_CHUNK_SIZE): ?string
    {
        return $this->reader->read($bytes);
    }
}
