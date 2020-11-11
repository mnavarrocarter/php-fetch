<?php


namespace MNC\Http;

/**
 * Class Status
 * @package MNC\Http
 */
class Status
{
    private int $code;
    private string $reasonPhrase;

    /**
     * Status constructor.
     * @param int $code
     * @param string $reasonPhrase
     */
    public function __construct(int $code, string $reasonPhrase)
    {
        $this->code = $code;
        $this->reasonPhrase = $reasonPhrase;
    }

    /**
     * @return bool
     */
    public function isError(): bool
    {
        return $this->code >= 400 && $this->code < 600;
    }

    public function isSuccess(): bool
    {
        return $this->code >= 200 && $this->code < 300;
    }

    public function isRedirect(): bool
    {
        return $this->code >= 300 && $this->code < 400;
    }

    /**
     * @return int
     */
    public function code(): int
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function reasonPhrase(): string
    {
        return $this->reasonPhrase;
    }
}