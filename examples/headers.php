<?php

require_once __DIR__ . '/../vendor/autoload.php';

use MNC\Http\StandardHeaders;
use function MNC\Http\fetch;

$response = fetch('https://mnavarro.dev');

$stdHeaders = StandardHeaders::from($response);
$lastModified = $stdHeaders->getLastModified()->diff(new DateTimeImmutable(), true)->h;
echo sprintf('This html content was last modified %s hours ago...', $lastModified) . PHP_EOL;