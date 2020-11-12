<?php

declare(strict_types=1);

/*
 * This file is part of the https://github.com/mnavarrocarter/php-fetch project.
 * (c) Matías Navarro-Carter <mnavarrocarter@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MNC\Http\Io;

/**
 * Class ResourceReader.
 */
final class ResourceReader implements Reader
{
    /**
     * @var resource
     */
    private $resource;

    /**
     * ResourceReader constructor.
     *
     * @param resource $resource
     */
    public function __construct($resource)
    {
        if (!is_resource($resource)) {
            throw new \InvalidArgumentException(sprintf('Argument 1 passed to %s() must be a resource, %s given', __METHOD__, gettype($resource)));
        }
        $this->resource = $resource;
    }

    /**
     * @throws ReaderError
     */
    public function read(int $bytes = self::DEFAULT_CHUNK_SIZE): ?string
    {
        if (feof($this->resource)) {
            return null;
        }
        $result = @fread($this->resource, $bytes);
        if ($result === false) {
            throw new ReaderError(error_get_last()['message'] ?? 'Unknown error');
        }

        return $result;
    }
}
