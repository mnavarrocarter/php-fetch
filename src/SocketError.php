<?php


namespace MNC\Http;


use Exception;

/**
 * A SocketError error occurs when connection over the tcp socket cannot be
 * established against the target host.
 *
 * This could be due to the server being unreachable (due firewall blocking
 * or network error), dns unable to resolve to an ip address or the server
 * being down.
 *
 * @package MNC\Http\Error
 */
class SocketError extends Exception
{

}