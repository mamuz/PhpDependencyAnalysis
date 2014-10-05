<?php

namespace PhpDA\Writer\Strategy;

use Fhaculty\Graph\GraphViz;
use PhpDA\Entity\AnalysisCollection;

abstract class AbstractStrategy implements StrategyInterface
{
    /** @var AnalysisCollection */
    private $analysisCollection;

    /** @var GraphViz */
    private $graphViz;

    /**
     * @return AnalysisCollection
     */
    protected function getAnalysisCollection()
    {
        return $this->analysisCollection;
    }

    /**
     * @return GraphViz
     */
    protected function getGraphViz()
    {
        return $this->graphViz;
    }

    public function filter(AnalysisCollection $collection)
    {
        $this->analysisCollection = $collection;
        $this->graphViz = new GraphViz($collection->getGraph());

        return $this->createOutput();
    }

    /**
     * @return string
     */
    abstract protected function createOutput();
}
