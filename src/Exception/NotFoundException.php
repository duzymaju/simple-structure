<?php

namespace SimpleStructure\Exception;

use SimpleStructure\Http\Response;
use Throwable;

/**
 * Not found exception
 */
class NotFoundException extends WebException
{
    /**
     * Construct
     *
     * @param string         $message  message
     * @param Throwable|null $previous previous
     */
    public function __construct($message = 'Not Found', Throwable $previous = null)
    {
        parent::__construct($message, Response::NOT_FOUND, $previous);
    }
}
