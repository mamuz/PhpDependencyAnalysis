<?php

namespace PhpDA\Writer\Strategy;

use PhpDA\Entity\AnalysisCollection;

interface StrategyInterface
{
    /**
     * @param AnalysisCollection $analysisCollection
     * @return string
     */
    public function filter(AnalysisCollection $analysisCollection);
}
