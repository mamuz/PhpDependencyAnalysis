<?php

namespace PackageC\Filter;

class FilterC implements FilterInterface
{
    public function __construct()
    {
        new FilterD;
    }
}
