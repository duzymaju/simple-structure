<?php

namespace SimpleStructure\Exception;

use Exception;
use SimpleStructure\Http\Response;

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
     * @param Exception|null $previous previous
     */
    public function __construct($message = null, $previous = null)
    {
        parent::__construct(isset($message) ? $message : self::MESSAGE, Response::INTERNAL_ERROR, $previous);
    }
}
