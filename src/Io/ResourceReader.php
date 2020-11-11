<?php
declare(strict_types=1);


namespace MNC\Http\Io;

use Exception;
use RuntimeException;

/**
 * Class ResourceReader
 * @package Spatialest\ETL
 */
final class ResourceReader implements Reader
{
    /**
     * @var resource
     */
    private $resource;

    /**
     * ResourceReader constructor.
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
     * @param int $bytes
     * @return string|null
     * @throws ReaderError
     */
    public function read(int $bytes = self::DEFAULT_CHUNK_SIZE): ?string
    {
        if (feof($this->resource)) {
            return null;
        }
        $result = fread($this->resource, $bytes);
        if ($result === false) {
            throw new ReaderError(error_get_last());
        }
        return $result;
    }
}