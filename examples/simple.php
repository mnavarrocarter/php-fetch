<?php

use function MNC\Http\fetch;

require_once __DIR__ . '/../vendor/autoload.php';

$response = fetch('https://mnavarro.dev');

while (($chunk = $response->body()->read()) !== null) {
    echo $chunk;
}
