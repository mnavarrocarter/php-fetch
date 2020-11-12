<?php

/*
 * This file is part of the https://github.com/mnavarrocarter/php-fetch project.
 * (c) Matías Navarro-Carter <mnavarrocarter@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MNC\Http;

/**
 * A PartialResponse represents an HTTP response that does not have body.
 */
interface PartialResponse
{
    /**
     * Returns the information of the HTTP status line.
     */
    public function status(): Status;

    /**
     * Returns the response headers.
     */
    public function headers(): Headers;
}
