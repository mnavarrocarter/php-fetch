<?php

/*
 * This file is part of the https://github.com/mnavarrocarter/php-fetch project.
 * (c) Matías Navarro-Carter <mnavarrocarter@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MNC\Http;

use Castor\Io\Reader;

/**
 * A Response represents a full HTTP protocol response.
 */
interface Response extends PartialResponse
{
    /**
     * Returns the response body.
     */
    public function body(): Reader;
}
