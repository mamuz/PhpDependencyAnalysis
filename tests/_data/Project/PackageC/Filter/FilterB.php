<?php

namespace PackageC\Filter;

class FilterB implements FilterInterface
{
    public function __construct()
    {
        new FilterC;
    }
}
