<?php


namespace MNC\Http;

/**
 * Class Headers
 * @package MNC\Http
 */
class Headers
{
    private array $headers;

    /**
     * @param array $headers
     * @return Headers
     */
    public static function fromArray(array $headers): Headers
    {
        $self = new self();
        foreach ($headers as $header) {
            [$name, $value] = explode(':', $header, 2);
            $self->put($name, trim($value));
        }
        return $self;
    }

    /**
     * @param array $headers
     * @return Headers
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

    /**
     * @param string $name
     * @param string $value
     */
    protected function put(string $name, string $value): void
    {
        $name = strtolower($name);
        $this->headers[$name] = $value;
    }

    /**
     * Returns a header
     * @param string $name
     * @return string
     */
    public function get(string $name): string
    {
        $name = strtolower($name);
        return $this->headers[$name] ?? '';
    }

    /**
     * @param string $name
     * @param string $substring
     * @return bool
     */
    public function contains(string $name, string $substring): bool
    {
        $name = strtolower($name);
        return strpos($this->get($name), $substring) !== false;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function has(string $name): bool
    {
        $name = strtolower($name);
        return array_key_exists($name, $this->headers);
    }

    /**
     * @param callable $callable
     * @return array
     */
    public function map(callable $callable): array
    {
        $arr = [];
        foreach ($this->headers as $name => $value) {
            $arr[] = $callable($value, $name);
        }
        return $arr;
    }

    /**
     * @param callable $callable
     * @return array
     */
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
        return $this->map(fn(string $value, string $name) => $name .': '.$value);
    }

    public function toMap(): array
    {
        return $this->headers;
    }
}