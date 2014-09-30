<?php

namespace PhpDA\Writer\Strategy;

use PhpDA\Entity\AnalysisCollection;

interface FilterInterface
{
    /**
     * @param AnalysisCollection $analysisCollection
     * @return string
     */
    public function filter(AnalysisCollection $analysisCollection);
}
