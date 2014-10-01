<?php

namespace PhpDA\Writer\Strategy;

use PhpDA\Entity\AnalysisCollection;

class Txt implements StrategyInterface
{
    public function filter(AnalysisCollection $collection)
    {
        return print_r($collection, true);
    }
}
