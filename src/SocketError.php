<?php

/*
 * This file is part of the https://github.com/mnavarrocarter/php-fetch project.
 * (c) Matías Navarro-Carter <mnavarrocarter@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MNC\Http;

use Exception;

/**
 * A SocketError error occurs when connection over the tcp socket cannot be
 * established against the target host.
 *
 * This could be due to the server being unreachable (due firewall blocking
 * or network error), dns unable to resolve to an ip address or the server
 * being down.
 */
class SocketError extends Exception implements FetchError
{
}
