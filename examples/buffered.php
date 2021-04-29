<?php

require_once __DIR__ . '/../vendor/autoload.php';

use function Castor\Io\readAll;
use function MNC\Http\fetch;

$response = fetch('https://mnavarro.dev');

echo readAll($response->body());
