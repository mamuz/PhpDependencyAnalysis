<?php

namespace PhpDA\Writer\Strategy;

use PhpDA\Entity\AnalysisCollection;

class Txt implements FilterInterface
{
    public function filter(AnalysisCollection $collection)
    {
        return print_r($collection, true);
    }
}
