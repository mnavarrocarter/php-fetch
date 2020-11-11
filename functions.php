<?php

declare(strict_types=1);

namespace MNC\Http;

use MNC\Http\Io\Reader;
use MNC\Http\Io\ResourceReader;

/**
 * Fetches a url
 *
 * @param string $url
 * @param array $options
 * @return Response
 *
 * @throws SocketError when a connection cannot be established
 * @throws ProtocolError when the server responds with an error
 */
function fetch(string $url, array $options = []): Response
{
    $method = $options['method'] ?? 'GET';
    $headers = $options['headers'] ?? [];
    $body = $options['body'] ?? null;

    // TODO: Need to add more options like cache, redirects following and others
    $context = [
        'http' => [
            'method' => $method,
            'header' => Headers::fromMap($headers)->toArray(),
            'contents' => $body,
            'ignore_errors' => true,
        ]
    ];

    $resource = @fopen($url, 'rb', false, stream_context_create($context));
    if (!is_resource($resource)) {
        throw new SocketError(error_get_last()['message']);
    }
    stream_set_blocking($resource, false);

    $body = new ResourceReader($resource);
    $headers = stream_get_meta_data($resource)['wrapper_data'];
    $firstLine = array_shift($headers);
    $response = Response::fromFirstLine($firstLine, Headers::fromArray($headers), $body);
    if ($response->status()->isError()) {
        throw new ProtocolError($response);
    }
    return $response;
}

/**
 * @param Reader $reader
 * @return string The buffered string
 * @throws Io\ReaderError
 */
function buffer(Reader $reader) {
    $buffer = '';
    while (($chunk = $reader->read()) !== null) {
        $buffer .= $chunk;
    }
    return $buffer;
}