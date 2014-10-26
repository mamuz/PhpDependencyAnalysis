<?php

namespace PackageC\Filter;

class FilterA implements FilterInterface
{
    public function __construct()
    {
        new FilterB;
    }
}
