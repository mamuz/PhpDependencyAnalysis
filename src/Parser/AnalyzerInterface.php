<?php

namespace PhpDA\Parser;

use Symfony\Component\Finder\SplFileInfo;

interface AnalyzerInterface
{
    /**
     * @param SplFileInfo $file
     * @return void
     */
    public function analyze(SplFileInfo $file);

    /**
     * @return \PhpDA\Entity\AnalysisCollection
     */
    public function getAnalysisCollection();
}
