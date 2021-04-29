<?php

declare(strict_types=1);

/*
 * This file is part of the https://github.com/mnavarrocarter/php-fetch project.
 * (c) Matías Navarro-Carter <mnavarrocarter@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MNC\Http;

/**
 * Fetches a url.
 *
 * @param array<string, mixed> $options
 *
 * @throws ProtocolError when the server responds with an error
 * @throws SocketError   when a connection cannot be established
 */
function fetch(string $url, array $options = []): Response
{
    $context = [
        'http' => [
            'method' => $options['method'] ?? 'GET',
            'header' => Headers::fromMap($options['headers'] ?? [])->toArray(),
            'contents' => $options['body'] ?? null,
            'ignore_errors' => true,
            'follow_location' => ($options['follow_redirects'] ?? true) ? 1 : 0,
            'max_redirects' => $options['max_redirects'] ?? 20,
            'protocol_version' => (float) ($options['protocol_version'] ?? '1.1'),
        ],
    ];

    $resource = @fopen($url, 'rb', false, stream_context_create($context));
    if (!is_resource($resource)) {
        throw new SocketError(error_get_last()['message'] ?? 'Unknown error');
    }
    stream_set_blocking($resource, false);

    // We extract relevant stream meta data
    $meta = stream_get_meta_data($resource);

    // We create objects out of that data.
    $partials = HttpPartialResponse::parseLines($meta['wrapper_data']);
    $mainPartial = array_pop($partials);
    $response = new HttpResponse($mainPartial, new ResponseBody($resource));

    // If there are still partials, we are dealing with a redirect here.
    // We decorate the response on previous request.
    if ($partials !== []) {
        $response = new RedirectedHttpResponse($response, ...$partials);
    }

    // If the request is an error according to the http spec, we throw an exception.
    if ($response->status()->isClientError() || $response->status()->isServerError()) {
        throw new ProtocolError($response);
    }

    return $response;
}
