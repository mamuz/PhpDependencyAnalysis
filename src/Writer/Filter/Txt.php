<?php

namespace PhpDA\Writer\Filter;

use PhpDA\Entity\Collection;
use PhpDA\Feature\WriteFilterInterface;

class Txt implements WriteFilterInterface
{
    public function filter(Collection $collection)
    {
        return print_r($collection, true);
    }
}
