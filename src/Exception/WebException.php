<?php

namespace SimpleStructure\Exception;

use Exception;
use Throwable;

/**
 * Web exception
 */
class WebException extends Exception implements ExceptionInterface
{
    /**
     * Construct
     *
     * @param string         $message    message
     * @param int            $statusCode status code
     * @param Throwable|null $previous   previous
     */
    public function __construct($message, $statusCode, Throwable $previous = null)
    {
        parent::__construct($message, $statusCode, $previous);
    }
}
