<?php

namespace MNC\Http;

use MNC\Http\Encoding\Json;
use MNC\Http\Io\Reader;

/**
 * Class Response
 * @package MNC\Http
 */
class Response
{
    private string $protocolVersion;
    /**
     * @var Status
     */
    private Status $status;
    /**
     * @var Headers
     */
    private Headers $headers;
    /**
     * @var Reader
     */
    private Reader $body;

    /**
     * @param string $line
     * @param Headers $headers
     * @param Reader $body
     * @return Response
     */
    public static function fromFirstLine(string $line, Headers $headers, Reader $body): Response
    {
        [$proto, $status, $reasonPhrase] = explode(' ', $line, 3);
        $proto = str_replace('HTTP/', '', $proto);
        return new self($proto, new Status((int) $status, $reasonPhrase), $headers, $body);
    }

    /**
     * Response constructor.
     * @param string $protocolVersion
     * @param Status $status
     * @param Headers $headers
     * @param Reader $body
     */
    public function __construct(string $protocolVersion, Status $status, Headers $headers, Reader $body)
    {
        $this->protocolVersion = $protocolVersion;
        $this->status = $status;
        $this->headers = $headers;
        $this->body = $body;
        $this->processJson();
    }

    /**
     * @return string
     */
    public function protocolVersion(): string
    {
        return $this->protocolVersion;
    }

    /**
     * @return Status
     */
    public function status(): Status
    {
        return $this->status;
    }

    /**
     * @return Headers
     */
    public function headers(): Headers
    {
        return $this->headers;
    }

    /**
     * @return Reader
     */
    public function body(): Reader
    {
        return $this->body;
    }

    private function processJson(): void
    {
        if ($this->headers->contains('Content-Type', 'json')) {
            $this->body = new Json($this->body);
        }
    }
}