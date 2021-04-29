<?php

use function Castor\Io\readAll;
use function MNC\Http\fetch;

require_once __DIR__ . '/../vendor/autoload.php';

$response = fetch('https://some-domain.com/some-form', [
    'method' => 'POST',
    'headers' => [
        'Content-Type' => 'application/json',
        'User-Agent' => 'PHP Fetch'
    ],
    'body' => json_encode(['data' => 'value'])
]);

echo readAll($response->body()); // Emits the response
