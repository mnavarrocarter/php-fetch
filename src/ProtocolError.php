<?php


namespace MNC\Http;

use Exception;

/**
 * A ProtocolError occurs when a connection can be established with the server
 * and a response is successfully sent, but this response denotes an error at
 * the HTTP protocol level (status code in the 400 or 500 range).
 *
 * This library makes the controversial choice of not ignoring protocol errors.
 * Some libraries argue that protocol errors should be handled at user level, but
 * we do not agree with that view, which has not been the traditional approach
 * for other protocol implementations in code.
 *
 * For example, when you send an invalid command to an FTP server or to an
 * SMTP server and those respond with errors, client libraries don't hesitate
 * in throwing exceptions.
 *
 * Not throwing exceptions means we are not following the rules of the protocol
 * we are trying to implement.
 *
 * @package MNC\Http\Error
 */
class ProtocolError extends Exception
{
    /**
     * @var Response
     */
    private Response $response;

    /**
     * ProtocolError constructor.
     * @param Response $response
     */
    public function __construct(Response $response)
    {
        parent::__construct('The server responded with an error', $response->status()->code());
        $this->response = $response;
    }

    /**
     * @return Response
     */
    public function getResponse(): Response
    {
        return $this->response;
    }
}