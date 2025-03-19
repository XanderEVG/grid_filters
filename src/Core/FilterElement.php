<?php

namespace Xanderevg\GridFiltersLibrary\Core;

class FilterElement
{
    public function __construct(
        public string $column,
        public mixed $value,
        public string $operator,
        public string $type,
        public bool $enabled = true,
    ) {
    }
}
