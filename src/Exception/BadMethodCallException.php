<?php

namespace SimpleStructure\Exception;

use BadMethodCallException as ParentException;

/**
 * Bad method call exception
 */
class BadMethodCallException extends ParentException implements ExceptionInterface
{
}
