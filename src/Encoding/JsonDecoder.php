<?php

/*
 * This file is part of the https://github.com/mnavarrocarter/php-fetch project.
 * (c) Matías Navarro-Carter <mnavarrocarter@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MNC\Http\Encoding;

use JsonException;

/**
 * A JsonDecoder decodes json data.
 */
interface JsonDecoder
{
    /**
     * Decodes a json string into an associative array.
     *
     * @return array<mixed, mixed>
     * @throws JsonException when parsing fails
     */
    public function decode(): array;
}
