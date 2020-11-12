<?php

/*
 * This file is part of the https://github.com/mnavarrocarter/php-fetch project.
 * (c) Matías Navarro-Carter <mnavarrocarter@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MNC\Http;

use MNC\Http\Io\Reader;

/**
 * The RedirectedHttpResponse composes a Response and Redirected types.
 */
final class RedirectedHttpResponse implements Response, Redirected
{
    private Response $response;
    /**
     * @var list<PartialResponse>
     */
    private array $previous;

    /**
     * RedirectedHttpResponse constructor.
     *
     * @param PartialResponse ...$previous
     */
    public function __construct(Response $response, PartialResponse ...$previous)
    {
        $this->response = $response;
        $this->previous = $previous;
    }

    /**
     * {@inheritdoc}
     */
    public function status(): Status
    {
        return $this->response->status();
    }

    /**
     * {@inheritdoc}
     */
    public function headers(): Headers
    {
        return $this->response->headers();
    }

    /**
     * {@inheritdoc}
     */
    public function previousResponses(): array
    {
        return $this->previous;
    }

    /**
     * {@inheritdoc}
     */
    public function body(): Reader
    {
        return $this->response->body();
    }
}
