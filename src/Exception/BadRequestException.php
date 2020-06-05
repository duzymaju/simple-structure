<?php

namespace SimpleStructure\Exception;

use Exception;
use SimpleStructure\Http\Response;

/**
 * Bad request exception
 */
class BadRequestException extends WebException
{
    /** @const string */
    const MESSAGE = 'Bad Request';

    /**
     * Construct
     *
     * @param string|null    $message  message
     * @param Exception|null $previous previous
     */
    public function __construct($message = null, $previous = null)
    {
        parent::__construct(isset($message) ? $message : self::MESSAGE, Response::BAD_REQUEST, $previous);
    }
}
