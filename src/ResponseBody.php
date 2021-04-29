<?php

/*
 * This file is part of the https://github.com/mnavarrocarter/php-fetch project.
 * (c) Matías Navarro-Carter <mnavarrocarter@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MNC\Http;

use Castor\Io\Reader;
use Castor\Io\ResourceHelper;
use InvalidArgumentException;

/**
 * Class ResponseBody.
 */
final class ResponseBody implements Reader
{
    use ResourceHelper;

    /**
     * ResponseBody constructor.
     *
     * @param resource $resource
     */
    public function __construct($resource)
    {
        if (!is_resource($resource)) {
            throw new InvalidArgumentException(sprintf('Argument 1 passed to %s must be a resource, %s given', __METHOD__, gettype($resource)));
        }
        $this->resource = $resource;
    }

    /**
     * {@inheritDoc}
     *
     * @psalm-suppress MoreSpecificReturnType
     * @psalm-suppress LessSpecificReturnStatement
     */
    public function read(int $length, string &$bytes): int
    {
        return $this->innerRead($length, $bytes);
    }
}
