<?php

namespace PhpDA\Writer\Strategy;

use PhpDA\Entity\AnalysisCollection;

class Text implements StrategyInterface
{
    public function filter(AnalysisCollection $collection)
    {
        return print_r($collection, true);
    }
}
