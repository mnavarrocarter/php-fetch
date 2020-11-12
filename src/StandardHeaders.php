<?php

/*
 * This file is part of the https://github.com/mnavarrocarter/php-fetch project.
 * (c) Matías Navarro-Carter <mnavarrocarter@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MNC\Http;

use DateTimeImmutable;

/**
 * The StandardHeaders class wraps the Header and provides methods for accessing
 * the most common headers in a programmatic and type safe way.
 */
class StandardHeaders
{
    private const HTTP_TIME_FORMAT = 'D, j M Y H:i:s e';

    private Headers $headers;

    public static function from(Response $response): StandardHeaders
    {
        return new self($response->headers());
    }

    /**
     * StandardHeaders constructor.
     */
    public function __construct(Headers $headers)
    {
        $this->headers = $headers;
    }

    public function getLastModified(): ?DateTimeImmutable
    {
        if (!$this->headers->has('Last-Modified')) {
            return null;
        }

        $datetime = DateTimeImmutable::createFromFormat(
            self::HTTP_TIME_FORMAT,
            $this->headers->get('Last-Modified')
        );
        if (!$datetime instanceof DateTimeImmutable) {
            return null;
        }

        return $datetime;
    }

    public function getContentType(): ?string
    {
        if (!$this->headers->has('Content-Type')) {
            return null;
        }

        return explode(';', $this->headers->get('Content-Type'))[0];
    }

    public function getExpires(): ?DateTimeImmutable
    {
        if (!$this->headers->has('Expires')) {
            return null;
        }

        $datetime = DateTimeImmutable::createFromFormat(
            self::HTTP_TIME_FORMAT,
            $this->headers->get('Expires')
        );
        if (!$datetime instanceof DateTimeImmutable) {
            return null;
        }

        return $datetime;
    }

    public function getContentLength(): ?int
    {
        if (!$this->headers->has('Content-Length')) {
            return null;
        }

        return (int) $this->headers->get('Content-Length');
    }
}
