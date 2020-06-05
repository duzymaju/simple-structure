<?php

namespace SimpleStructure\Exception;

use Exception;

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
     * @param Exception|null $previous   previous
     */
    public function __construct($message, $statusCode, $previous = null)
    {
        parent::__construct($message, $statusCode, $previous);
    }
}
