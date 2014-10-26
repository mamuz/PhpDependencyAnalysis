<?php

namespace PackageC\Filter;

class FilterD implements FilterInterface
{
    public function __construct()
    {
        new FilterA;
    }
}
