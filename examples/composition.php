<?php

use MNC\Http\Encoding\JsonDecoder;
use function MNC\Http\fetch;

$authenticated = static function (string $token) {
    return static function (string $method, string $path, array $contents = null) use ($token): ?array {
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
        if ($body instanceof JsonDecoder) {
            return $body->parseJson();
        }
        return null;
    };
};

$client = $authenticated('your-api-token');

$ordersArray = $client('GET', '/orders');
$createdOrderArray = $client('POST', '/orders', ['id' => '1234556']);