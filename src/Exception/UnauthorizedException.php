<?php

namespace SimpleStructure\Exception;

use SimpleStructure\Http\Response;
use Throwable;

/**
 * Unauthorized exception
 */
class UnauthorizedException extends WebException
{
    /**
     * Construct
     *
     * @param string         $message  message
     * @param Throwable|null $previous previous
     */
    public function __construct($message = 'Unauthorized', Throwable $previous = null)
    {
        parent::__construct($message, Response::UNAUTHORIZED, $previous);
    }
}
