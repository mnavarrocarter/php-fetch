<?php

/*
 * This file is part of the https://github.com/mnavarrocarter/php-fetch project.
 * (c) Matías Navarro-Carter <mnavarrocarter@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MNC\Http\Encoding;

use Castor\Io\Error;
use function Castor\Io\readAll;
use Castor\Io\Reader;
use JsonException;

/**
 * Class Json.
 */
final class Json implements Reader
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
     * @throws Error
     */
    public function decode(): array
    {
        return json_decode(readAll($this->reader), true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * {@inheritDoc}
     */
    public function read(int $length, string &$bytes): int
    {
        return $this->reader->read($length, $bytes);
    }
}
