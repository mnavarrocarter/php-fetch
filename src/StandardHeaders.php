<?php

namespace MNC\Http;

use DateTimeImmutable;

/**
 * The StandardHeaders class wraps the Header and provides methods for accessing
 * the most common headers in a programmatic and type safe way.
 *
 * @package MNC\Http
 */
class StandardHeaders
{
    private const HTTP_TIME_FORMAT = 'D, j M Y H:i:s e';

    /**
     * @var Headers
     */
    private Headers $headers;

    /**
     * @param Response $response
     * @return StandardHeaders
     */
    public static function from(Response $response): StandardHeaders
    {
        return new self($response->headers());
    }

    /**
     * StandardHeaders constructor.
     * @param Headers $headers
     */
    public function __construct(Headers $headers)
    {
        $this->headers = $headers;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getLastModified(): ?DateTimeImmutable
    {
        if (!$this->headers->has('Last-Modified')) {
            return null;
        }
        return DateTimeImmutable::createFromFormat(
            self::HTTP_TIME_FORMAT,
            $this->headers->get('Last-Modified')
        );
    }

    /**
     * @return string|null
     */
    public function getContentType(): ?string
    {
        if (!$this->headers->has('Content-Type')) {
            return null;
        }
        return explode(';', $this->headers->get('Content-Type'))[0];
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getExpires(): ?DateTimeImmutable
    {
        if (!$this->headers->has('Expires')) {
            return null;
        }
        return DateTimeImmutable::createFromFormat(
            self::HTTP_TIME_FORMAT,
            $this->headers->get('Expires')
        );
    }

    /**
     * @return int|null
     */
    public function getContentLength(): ?int
    {
        if (!$this->headers->has('Content-Length')) {
            return null;
        }
        return (int) $this->headers->get('Content-Length');
    }
}