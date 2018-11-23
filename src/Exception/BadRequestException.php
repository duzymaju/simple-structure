<?php

namespace SimpleStructure\Exception;

use SimpleStructure\Http\Response;
use Throwable;

/**
 * Bad request exception
 */
class BadRequestException extends WebException
{
    /**
     * Construct
     * @param string         $message  message
     * @param Throwable|null $previous previous
     */
    public function __construct($message = 'Bad Request', Throwable $previous = null)
    {
        parent::__construct($message, Response::BAD_REQUEST, $previous);
    }
}
