<?php

namespace SimpleStructure\Exception;

use Exception;
use SimpleStructure\Http\Response;

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
     * @param Exception|null $previous previous
     */
    public function __construct($message = null, $previous = null)
    {
        parent::__construct(isset($message) ? $message : self::MESSAGE, Response::NOT_FOUND, $previous);
    }
}
