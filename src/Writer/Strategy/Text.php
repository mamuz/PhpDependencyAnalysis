<?php

namespace PhpDA\Writer\Strategy;

use Fhaculty\Graph\GraphViz;
use PhpDA\Entity\AnalysisCollection;

class Text implements StrategyInterface
{
    public function filter(AnalysisCollection $collection)
    {
        $graphViz = new GraphViz($collection->getGraph());
        //echo $graphViz->createScript();
        return print_r($collection, true);
    }
}
