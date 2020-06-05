<?php

namespace SimpleStructure\Exception;

use Exception;
use SimpleStructure\Http\Response;

/**
 * Unauthorized exception
 */
class UnauthorizedException extends WebException
{
    /** @const string */
    const MESSAGE = 'Unauthorized';

    /**
     * Construct
     *
     * @param string|null    $message  message
     * @param Exception|null $previous previous
     */
    public function __construct($message = null, $previous = null)
    {
        parent::__construct(isset($message) ? $message : self::MESSAGE, Response::UNAUTHORIZED, $previous);
    }
}
