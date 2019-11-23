<?php

namespace SimpleStructure\Exception;

use SimpleStructure\Http\Response;
use Throwable;

/**
 * Internal exception
 */
class InternalException extends WebException
{
    /** @const string */
    const MESSAGE = 'Internal Server Error';

    /**
     * Construct
     *
     * @param string|null    $message  message
     * @param Throwable|null $previous previous
     */
    public function __construct($message = null, Throwable $previous = null)
    {
        parent::__construct(isset($message) ? $message : self::MESSAGE, Response::INTERNAL_ERROR, $previous);
    }
}
