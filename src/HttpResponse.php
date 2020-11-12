<?php

/*
 * This file is part of the https://github.com/mnavarrocarter/php-fetch project.
 * (c) Matías Navarro-Carter <mnavarrocarter@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MNC\Http;

use MNC\Http\Encoding\Json;
use MNC\Http\Io\Reader;

/**
 * Class Response.
 */
final class HttpResponse implements Response
{
    private PartialResponse $partial;

    private Reader $body;

    /**
     * Response constructor.
     */
    public function __construct(PartialResponse $partial, Reader $body)
    {
        $this->partial = $partial;
        $this->body = $body;
        $this->processEncodings();
    }

    /**
     * {@inheritdoc}
     */
    public function status(): Status
    {
        return $this->partial->status();
    }

    /**
     * {@inheritdoc}
     */
    public function headers(): Headers
    {
        return $this->partial->headers();
    }

    /**
     * {@inheritdoc}
     */
    public function body(): Reader
    {
        return $this->body;
    }

    private function processEncodings(): void
    {
        if ($this->headers()->contains('Content-Type', 'json')) {
            $this->body = new Json($this->body);
        }
    }
}
