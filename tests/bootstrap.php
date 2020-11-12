<?php

namespace MNC\Http;

use Symfony\Component\Process\Process;

require __DIR__ . '/../vendor/autoload.php';

$server = new Process(['php', __DIR__ . '/server.php']);
$server->start();
$stop = static fn() => $server->stop();
\register_shutdown_function($stop);
sleep(1);
