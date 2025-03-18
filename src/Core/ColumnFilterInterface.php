<?php

namespace Xanderevg\GridFiltersLibrary\Core;

use Xanderevg\GridFiltersLibrary\Core\QueryBuilderInterface;

interface ColumnFilterInterface
{
    public function add(): QueryBuilderInterface;
}