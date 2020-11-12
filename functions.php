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
 * @throws ProtocolError when the server responds with an error
 * @throws SocketError when a connection cannot be established
 */
function fetch(string $url, array $options = []): Response
{
    $method = $options['method'] ?? 'GET';
    $headers = $options['headers'] ?? [];
    $body = $options['body'] ?? null;
    $followRedirects = $options['follow_redirects'] ?? true;
    $maxRedirects = $options['max_redirects'] ?? 20;
    $protocolVersion = $options['protocol_version'] ?? '1.1';

    // TODO: Need to add more options like cache, redirects following and others
    $context = [
        'http' => [
            'method' => $method,
            'header' => Headers::fromMap($headers)->toArray(),
            'contents' => $body,
            'ignore_errors' => true,
            'follow_location' => $followRedirects ? 1 : 0,
            'max_redirects' => $maxRedirects,
            'protocol_version' => (float) $protocolVersion
        ]
    ];

    $resource = @fopen($url, 'rb', false, stream_context_create($context));
    if (!is_resource($resource)) {
        throw new SocketError(error_get_last()['message']);
    }
    stream_set_blocking($resource, false);

    // We extract relevant stream meta data
    $meta = stream_get_meta_data($resource);
    $rawHeaders = $meta['wrapper_data'];

    // We create objects out of that data.
    $partials = HttpPartialResponse::parseLines($rawHeaders);
    $mainPartial = array_pop($partials);
    $body = new ResourceReader($resource);
    $response = new HttpResponse($mainPartial, $body);

    // If there are still partials, we are dealing with a redirect here.
    // We decorate the response on previous request.
    if (count($partials) > 0) {
        $response = new RedirectedHttpResponse($response, ...$partials);
    }

    // If the request is an error according to the spec, we throw an exception.
    if ($response->status()->isClientError() || $response->status()->isServerError()) {
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