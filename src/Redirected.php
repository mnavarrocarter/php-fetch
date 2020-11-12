<?php

/*
 * This file is part of the https://github.com/mnavarrocarter/php-fetch project.
 * (c) Matías Navarro-Carter <mnavarrocarter@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MNC\Http;

/**
 * A Redirected defines a response that contains redirection information.
 */
interface Redirected
{
    /**
     * Returns an array with information about previous responses.
     *
     * The body of those responses is not available.
     *
     * @return list<PartialResponse>
     */
    public function previousResponses(): array;
}
