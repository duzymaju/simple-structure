<?php

namespace SimpleStructure\Exception;

use SimpleStructure\Http\Response;
use Throwable;

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
     * @param Throwable|null $previous previous
     */
    public function __construct($message = null, Throwable $previous = null)
    {
        parent::__construct(isset($message) ? $message : self::MESSAGE, Response::BAD_REQUEST, $previous);
    }
}
