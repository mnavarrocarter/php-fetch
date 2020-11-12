<?php

require_once __DIR__ . '/../vendor/autoload.php';

// This script creates an HTTP Server using Amp PHP on port 5488. This server is
// used for testing the HTTP client features.
use Amp\Http\Server\HttpServer;
use Amp\Http\Server\Response;
use Amp\Http\Server\StaticContent\DocumentRoot;
use Amp\Http\Status;
use Amp\Socket\Server as TcpSocket;
use MNC\Router\Router;
use Psr\Log\NullLogger;
use function Amp\Http\Server\redirectTo;
use function MNC\Router\handleFunc;

/**
 * @return Response
 */
function redirect(): Response
{
    return redirectTo('user.json');
}

function not_found(): Response
{
    return new Response(Status::NOT_FOUND, [], 'Not Found');
}

$router = new Router();
$router->post('/login', handleFunc('redirect'));
$root = new DocumentRoot(__DIR__ . '/static');
$root->setFallback($router);

Amp\Loop::run(function () use ($root) {
    $sockets = [
        TcpSocket::listen("0.0.0.0:5488"),
    ];

    $server = new HttpServer($sockets, $root, new NullLogger);

    yield $server->start();

    // Stop the server gracefully when SIGINT is received.
    // This is technically optional, but it is best to call Server::stop().
    Amp\Loop::onSignal(SIGINT, function (string $watcherId) use ($server) {
        Amp\Loop::cancel($watcherId);
        yield $server->stop();
    });
});
