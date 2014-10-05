<?php

namespace PhpDA\Writer\Strategy;

use Fhaculty\Graph\GraphViz;
use PhpDA\Entity\AnalysisCollection;

abstract class AbstractStrategy implements StrategyInterface
{
    /** @var GraphViz */
    private $graphViz;

    /**
     * @param GraphViz $graphViz
     * @return AbstractStrategy
     */
    protected function setGraphViz(GraphViz $graphViz)
    {
        $this->graphViz = $graphViz;
        return $this;
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
        $this->setGraphViz(new GraphViz($collection->getGraph()));

        return $this->createOutput();
    }

    /**
     * @return string
     */
    abstract protected function createOutput();
}
