<?php

use MNC\Http\Encoding\Json;
use function MNC\Http\fetch;

require_once __DIR__ . '/../vendor/autoload.php';

// We start with an interface, a well defined contract.
interface ApiClient
{
    public function getOrder(string $id): array;

    public function createOrder(string $id): array;

    public function deleteOrder(string $id): void;
}

// Then, we can have an implementation that uses this library.
final class FetchApiClient implements ApiClient
{
    /**
     * @var callable
     */
    private $client;

    /**
     * @param string $token
     * @return FetchApiClient
     */
    public static function authenticate(string $token): FetchApiClient
    {
        $client = static function (string $method, string $path, array $contents = null) use ($token): ?array {
            $url = 'https://my-api-service.com' . $path;
            $response = fetch($url, [
                'method' => $method,
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $token
                ],
                'body' => is_array($contents) ? json_encode($contents) : ''
            ]);

            $body = $response->body();
            if ($body instanceof Json) {
                return $body->decode();
            }
            return null;
        };
        return new self($client);
    }

    /**
     * FetchApiClient constructor.
     * @param callable $client
     */
    public function __construct(callable $client)
    {
        $this->client = $client;
    }

    public function getOrder(string $id): array
    {
        return ($this->client)('GET', '/orders/'.$id);
    }

    public function createOrder(string $id): array
    {
        return ($this->client)('POST', '/orders', [
            'id' => $id
        ]);
    }

    public function deleteOrder(string $id): void
    {
        ($this->client)('DELETE', '/orders/'.$id);
    }
}
