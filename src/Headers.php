<?php

/*
 * This file is part of the https://github.com/mnavarrocarter/php-fetch project.
 * (c) Matías Navarro-Carter <mnavarrocarter@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MNC\Http;

/**
 * Class Headers.
 */
class Headers
{
    /**
     * @var array<string, string>
     */
    private array $headers;

    /**
     * @internal You should not use this api
     *
     * @param list<string> $lines
     */
    public static function fromLines(array &$lines): Headers
    {
        $headers = new self();
        while ($lines !== []) {
            $line = array_shift($lines);
            if (strpos($line, 'HTTP') === 0) {
                array_unshift($lines, $line); // Put the line back
                break;
            }
            [$name, $value] = explode(':', $line, 2);
            $headers->put($name, trim($value));
        }

        return $headers;
    }

    /**
     * @param array<string, string> $headers
     */
    public static function fromMap(array $headers): Headers
    {
        $self = new self();
        foreach ($headers as $name => $value) {
            $self->put($name, $value);
        }

        return $self;
    }

    /**
     * Headers constructor.
     */
    public function __construct()
    {
        $this->headers = [];
    }

    protected function put(string $name, string $value): void
    {
        $this->headers[strtolower($name)] = $value;
    }

    /**
     * Returns a header.
     */
    public function get(string $name): string
    {
        return $this->headers[strtolower($name)] ?? '';
    }

    public function contains(string $name, string $substring): bool
    {
        return strpos($this->get(strtolower($name)), $substring) !== false;
    }

    public function has(string $name): bool
    {
        return array_key_exists(strtolower($name), $this->headers);
    }

    /**
     * @return list<string>
     */
    public function map(callable $callable): array
    {
        return array_map(
            fn (string $name, string $value) => $callable($value, $name),
            array_keys($this->headers),
            $this->headers
        );
    }

    /**
     * @return array<string, string>
     */
    public function filter(callable $callable): array
    {
        return array_filter(
            $this->headers,
            static fn (string $value, string $name) => $callable($value, $name),
            ARRAY_FILTER_USE_BOTH
        );
    }

    /**
     * @return list<string>
     */
    public function toArray(): array
    {
        return $this->map(fn (string $value, string $name) => $name.': '.$value);
    }

    /**
     * @return array<string, string>
     */
    public function toMap(): array
    {
        return $this->headers;
    }
}
