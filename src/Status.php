<?php

/*
 * This file is part of the https://github.com/mnavarrocarter/php-fetch project.
 * (c) Matías Navarro-Carter <mnavarrocarter@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MNC\Http;

/**
 * A Status represents the information contained in the HTTP status line of
 * a response.
 */
class Status
{
    private string $protocolVersion;
    private int $code;
    private string $reasonPhrase;

    /**
     * Creates a Status instance by parsing the first line of an HTTP response.
     */
    public static function fromStatusLine(string $line): Status
    {
        [$proto, $status, $reasonPhrase] = explode(' ', $line, 3);
        $proto = str_replace('HTTP/', '', $proto);

        return new self($proto, (int) $status, $reasonPhrase);
    }

    /**
     * Status constructor.
     */
    public function __construct(string $protocolVersion, int $code, string $reasonPhrase)
    {
        $this->protocolVersion = $protocolVersion;
        $this->code = $code;
        $this->reasonPhrase = $reasonPhrase;
    }

    /**
     * Returns true if the status is inside the 500 range.
     */
    public function isServerError(): bool
    {
        return $this->code >= 500 && $this->code < 600;
    }

    /**
     * Returns true if the status is inside the 400 range.
     */
    public function isClientError(): bool
    {
        return $this->code >= 400 && $this->code < 500;
    }

    /**
     * Returns true if the status is in the 200 range.
     */
    public function isSuccess(): bool
    {
        return $this->code >= 200 && $this->code < 300;
    }

    /**
     * Returns true if the status is in the 300 range.
     */
    public function isRedirect(): bool
    {
        return $this->code >= 300 && $this->code < 400;
    }

    /**
     * Returns true if the status code matches the one passed.
     */
    public function isCode(int $code): bool
    {
        return $this->code === $code;
    }

    /**
     * Returns the HTTP protocol version used.
     *
     * Ex: 1.0 or 1.1
     */
    public function protocolVersion(): string
    {
        return $this->protocolVersion;
    }

    /**
     * Returns the status code.
     */
    public function code(): int
    {
        return $this->code;
    }

    /**
     * Returns the reason phrase.
     */
    public function reasonPhrase(): string
    {
        return $this->reasonPhrase;
    }
}
