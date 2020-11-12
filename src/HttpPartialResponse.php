<?php

/*
 * This file is part of the https://github.com/mnavarrocarter/php-fetch project.
 * (c) Matías Navarro-Carter <mnavarrocarter@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MNC\Http;

/**
 * Represents a partial response, with no body.
 *
 * This is usually used when redirects are followed.
 */
final class HttpPartialResponse implements PartialResponse
{
    private Status $status;

    private Headers $headers;

    /**
     * @param HttpPartialResponse[] $lines
     * @return HttpPartialResponse[]
     */
    public static function parseLines(array $lines): array
    {
        $partials = [];
        while (count($lines) > 0) {
            $line = array_shift($lines);
            if (strpos($line, 'HTTP') === 0) {
                $status = Status::fromStatusLine($line);
                $headers = Headers::fromLines($lines);
                $partials[] = new HttpPartialResponse($status, $headers);
            }
        }

        return $partials;
    }

    /**
     * HttpPartialResponse constructor.
     */
    public function __construct(Status $status, Headers $headers)
    {
        $this->status = $status;
        $this->headers = $headers;
    }

    /**
     * {@inheritdoc}
     */
    public function status(): Status
    {
        return $this->status;
    }

    /**
     * {@inheritdoc}
     */
    public function headers(): Headers
    {
        return $this->headers;
    }
}
