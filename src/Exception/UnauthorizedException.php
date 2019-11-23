<?php

namespace SimpleStructure\Exception;

use SimpleStructure\Http\Response;
use Throwable;

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
     * @param Throwable|null $previous previous
     */
    public function __construct($message = null, Throwable $previous = null)
    {
        parent::__construct(isset($message) ? $message : self::MESSAGE, Response::UNAUTHORIZED, $previous);
    }
}
