<?php

require_once __DIR__ . '/../vendor/autoload.php';

use MNC\Http\Encoding\JsonDecoder;
use function MNC\Http\fetch;

$response = fetch('https://api.github.com/users/mnavarrocarter', [
    'headers' => [
        'User-Agent' => 'PHP Fetch 1.0' // Github api requires user agent
    ]
]);

$body = $response->body();

if ($body instanceof JsonDecoder) {
    var_dump($body->decode()); // Dumps the json as an array
}