<?php

namespace SimpleStructure\Exception;

use SimpleStructure\Http\Response;
use Throwable;

/**
 * Not found exception
 */
class NotFoundException extends WebException
{
    /** @const string */
    const MESSAGE = 'Not Found';

    /**
     * Construct
     *
     * @param string|null    $message  message
     * @param Throwable|null $previous previous
     */
    public function __construct($message = null, Throwable $previous = null)
    {
        parent::__construct(isset($message) ? $message : self::MESSAGE, Response::NOT_FOUND, $previous);
    }
}
