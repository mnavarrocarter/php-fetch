<?php

use Castor\Io\Eof;
use function MNC\Http\fetch;

require_once __DIR__ . '/../vendor/autoload.php';

$response = fetch('https://mnavarro.dev');

while (true) {
    $chunk = '';
    try {
        $response->body()->read(4096, $chunk);
    } catch (Eof $e) {
        break;
    }
    echo $chunk;
}
