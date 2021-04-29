<?php

use function MNC\Http\fetch;

require_once __DIR__ . '/../vendor/autoload.php';

$response = fetch('https://mnavarro.dev');

echo $response->status()->protocolVersion();  // 1.1
echo $response->status()->code();   // 200
echo $response->status()->reasonPhrase(); // OK
echo $response->headers()->has('content-type'); // true
echo $response->headers()->contains('content-type', 'html'); // true
echo $response->headers()->get('content-type'); // text/html;charset=utf-8
$bytes = '';
$response->body()->read(4096, $bytes); // Reads data into $bytes
echo $bytes; // Outputs some bytes from the response body
