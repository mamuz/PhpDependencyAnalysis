<?php

namespace PhpDA\Writer\Strategy;

use PhpDA\Entity\Collection;
use PhpDA\Feature\WriteStrategyInterface;

class Txt implements WriteStrategyInterface
{
    public function filter(Collection $collection)
    {
        return print_r($collection, true);
    }
}
