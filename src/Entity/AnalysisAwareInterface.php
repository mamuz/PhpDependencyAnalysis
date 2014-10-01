<?php

namespace PhpDA\Entity;

interface AnalysisAwareInterface
{
    /**
     * @param Analysis $analysis
     */
    public function setAnalysis(Analysis $analysis);

    /**
     * @return Analysis $analysis
     */
    public function getAnalysis();
}
