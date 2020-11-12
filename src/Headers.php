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
    private array $headers;

    /**
     * @internal You should not use this api
     */
    public static function fromLines(array &$lines): Headers
    {
        $headers = new self();
        while (count($lines) !== 0) {
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
        $name = strtolower($name);
        $this->headers[$name] = $value;
    }

    /**
     * Returns a header.
     */
    public function get(string $name): string
    {
        $name = strtolower($name);

        return $this->headers[$name] ?? '';
    }

    public function contains(string $name, string $substring): bool
    {
        $name = strtolower($name);

        return strpos($this->get($name), $substring) !== false;
    }

    public function has(string $name): bool
    {
        $name = strtolower($name);

        return array_key_exists($name, $this->headers);
    }

    public function map(callable $callable): array
    {
        $arr = [];
        foreach ($this->headers as $name => $value) {
            $arr[] = $callable($value, $name);
        }

        return $arr;
    }

    public function filter(callable $callable): array
    {
        $arr = [];
        foreach ($this->headers as $name => $value) {
            if ($callable($value, $name) === true) {
                $arr[$name] = $value;
            }
        }

        return $arr;
    }

    public function toArray(): array
    {
        return $this->map(fn (string $value, string $name) => $name.': '.$value);
    }

    public function toMap(): array
    {
        return $this->headers;
    }
}
