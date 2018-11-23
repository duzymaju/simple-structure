<?php

namespace SimpleStructure\Exception;

use InvalidArgumentException as ParentException;

/**
 * Invalid argument exception
 */
class InvalidArgumentException extends ParentException implements ExceptionInterface
{
}
