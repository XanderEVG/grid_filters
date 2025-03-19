<?php

namespace Xanderevg\GridFiltersLibrary\Core\Exceptions;

class FilterOperatorException extends \RuntimeException
{
    public function __construct(string $message = 'Invalid filter operator', int $code = 400, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function __toString()
    {
        return $this->message;
    }
}
