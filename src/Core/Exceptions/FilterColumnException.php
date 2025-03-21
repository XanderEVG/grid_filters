<?php

namespace Xanderevg\GridFiltersLibrary\Core\Exceptions;

class FilterColumnException extends \RuntimeException
{
    public function __construct(string $message = 'Invalid field name', int $code = 400, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function __toString()
    {
        return $this->message;
    }
}
