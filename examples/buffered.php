<?php

require_once __DIR__ . '/../vendor/autoload.php';

use function MNC\Http\buffer;
use function MNC\Http\fetch;

$response = fetch('https://mnavarro.dev');

echo buffer($response->body());
