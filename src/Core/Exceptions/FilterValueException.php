<?php

namespace Xanderevg\GridFiltersLibrary\Core\Exceptions;

use RuntimeException;

class FilterValueException extends RuntimeException
{
    public function __construct(string $message = 'Invalid value of filter', int $code = 400, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function __toString()
    {
        return $this->message;
    }
}
